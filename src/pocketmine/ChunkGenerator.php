<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine;

use pocketmine\utils\Binary;
use pocketmine\network\protocol\FullChunkDataPacket;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\Info as ProtocolInfo;

class ChunkGenerator extends Worker{
	
	protected $classLoader;
	protected $shutdown;
	
	protected $externalQueue;
	protected $internalQueue;
	
	const SUPPORTED_PROTOCOLS = [ProtocolInfo::BASE_PROTOCOL, ProtocolInfo::PROTOCOL_105, ProtocolInfo::PROTOCOL_110, ProtocolInfo::PROTOCOL_120];

	public function __construct(\ClassLoader $loader = null){
		$this->externalQueue = new \Threaded;
		$this->internalQueue = new \Threaded;
		$this->shutdown = false;
		$this->classLoader = $loader;
		$this->start(PTHREADS_INHERIT_CONSTANTS);
	}
	
	public function registerClassLoader(){
		if(!interface_exists("ClassLoader", false)){
			require(\pocketmine\PATH . "src/spl/ClassLoader.php");
			require(\pocketmine\PATH . "src/spl/BaseClassLoader.php");
			require(\pocketmine\PATH . "src/pocketmine/CompatibleClassLoader.php");
		}
		
		if($this->classLoader !== null){
			$this->classLoader->register(true);
		}
	}

	public function run(){
		$this->registerClassLoader();
		gc_enable();
		ini_set("memory_limit", -1);
		ini_set("display_errors", 1);
		ini_set("display_startup_errors", 1);

		set_error_handler([$this, "errorHandler"], E_ALL);
		DataPacket::initializePackets();
		$this->tickProcessor();
	}

	public function pushMainToThreadPacket($data){
		$this->internalQueue[] = $data;
	}

	public function readMainToThreadPacket(){
		return $this->internalQueue->shift();
	}
	
	public function readThreadToMainPacket(){
		return $this->externalQueue->shift();
	}

	protected function tickProcessor(){
		while (!$this->shutdown){
			$start = microtime(true);
			$count = count($this->internalQueue);
			$this->tick();
			$time = microtime(true) - $start;
			if($time < 0.025){
				@time_sleep_until(microtime(true) + 0.025 - $time);
			}
		}
	}

	protected function tick(){
		while(count($this->internalQueue) > 0){
			$data = unserialize($this->readMainToThreadPacket());
			$this->doChunk($data);
		}
	}

	protected function doChunk($data){
		$chunkData120 = '';
		if(isset($data['isAnvil']) && $data['isAnvil'] == true){
			$chunkData = chr(count($data['chunk']['sections']));
			$chunkData120 = chr(count($data['chunk']['sections']));
			foreach($data['chunk']['sections'] as $y => $sections){
				$chunkData .= chr(0);
				$chunkData120 .= chr(0);
				if($sections['empty'] == true){
					$chunkData .= str_repeat("\x00", 10240);
					$chunkData120 .= str_repeat("\x00", 6144);
				}else{
					if(isset($data['isSorted']) && $data['isSorted'] == true){
						$blockData = $sections['blocks'] . $sections['data'];
						$lightData = $sections['skyLight'] . $sections['blockLight'];
					}else{
						$blockData = $this->sortData($sections['blocks']) . $this->sortHalfData($sections['data']);
						$lightData = $this->sortHalfData($sections['skyLight']) . $this->sortHalfData($sections['blockLight']);
					}
					//$blockData = $this->sortData($sections['blocks']) . $this->sortHalfData($sections['data']);
					//$lightData = $this->sortHalfData($sections['skyLight']) . $this->sortHalfData($sections['blockLight']);
					$chunkData .= $blockData . $lightData;
					$chunkData120 .= $blockData;
				}
			}
			
			$chunkData .= $data['chunk']['heightMap'] .
				$data['chunk']['biomeColor'] .
				Binary::writeLInt(0) .
				$data['tiles'];		
			$chunkData120 .= $data['chunk']['heightMap'] .
				$data['chunk']['biomeColor'] .
				Binary::writeLInt(0) .
				$data['tiles'];
		}else{
			$blockIdArray = $data['blocks'];	
			$blockDataArray = $data['data'];
			$skyLightArray = $data['skyLight'];	
			$blockLightArray = $data['blockLight'];

			$countBlocksInChunk = 8;
			$chunkData = chr($countBlocksInChunk);		
			$chunkData120 = chr($countBlocksInChunk);		
			
			for($blockIndex = 0; $blockIndex < $countBlocksInChunk; $blockIndex++){
				$blockIdData = '';
				$blockDataData = '';
				$skyLightData = '';
				$blockLightData = '';
				for($i = 0; $i < 256; $i++){
					$startIndex = ($blockIndex + ($i << 3)) << 3;
					$blockIdData .= substr($blockIdArray, $startIndex << 1, 16);
					$blockDataData .= substr($blockDataArray, $startIndex, 8);
					$skyLightData .= substr($skyLightArray, $startIndex, 8);
					$blockLightData .= substr($blockLightArray, $startIndex, 8);
				}
				
				$chunkData .= chr(0) . $blockIdData . $blockDataData . $skyLightData . $blockLightData;
				$chunkData120 .= chr(0) . $blockIdData . $blockDataData;
			}
			
			$chunkData .= $data['heightMap'] .
				$data['biomeColor'] .
				Binary::writeLInt(0) .
				$data['tiles'];		
			$chunkData120 .= $data['heightMap'] .
				$data['biomeColor'] .
				Binary::writeLInt(0) .
				$data['tiles'];
		}
		
		$result = array();
		$result['chunkX'] = $data['chunkX'];
		$result['chunkZ'] = $data['chunkZ'];
		foreach(ChunkGenerator::SUPPORTED_PROTOCOLS as $protocol){
			$pk = new FullChunkDataPacket();
			$pk->chunkX = $data['chunkX'];
			$pk->chunkZ = $data['chunkZ'];
			$pk->order = FullChunkDataPacket::ORDER_COLUMNS;
			$pk->data = $protocol >= ProtocolInfo::PROTOCOL_120 ? $chunkData120 : $chunkData;
			$pk->encode($protocol);
			if(!empty($pk->buffer)){				
				$str = Binary::writeVarInt(strlen($pk->buffer)) . $pk->buffer;
				$ordered = zlib_encode($str, ZLIB_ENCODING_DEFLATE, 7);
				$result[$protocol] = $ordered;
			}
		}
		
		$this->externalQueue[] = serialize($result);
	}

	public function shutdown(){		
		$this->shutdown = true;
	}
	
	public function errorHandler($errno, $errstr, $errfile, $errline, $context){
		$errorConversion = [
			E_ERROR => "E_HATA",
				E_WARNING => "E_UYARI",
				E_PARSE => "E_OKUMA",
				E_NOTICE => "E_BILDIRIM",
				E_CORE_ERROR => "E_CORE_HATASI",
				E_CORE_WARNING => "E_CORE_UYARISI",
				E_COMPILE_ERROR => "E_COMPILE_HATASI",
				E_COMPILE_WARNING => "E_COMPILE_UYARISI",
				E_USER_ERROR => "E_KULLANICI_HATASI",
				E_USER_WARNING => "E_KULLANICI_UYARISI",
				E_USER_NOTICE => "E_KULLANCI_BILDIRIMI",
				E_STRICT => "E_STRICT",
				E_RECOVERABLE_ERROR => "E_RECOVERABLE_HATA",
				E_DEPRECATED => "E_DEPRECATED",
				E_USER_DEPRECATED => "E_KULLANICI_DEPRECATED",
		];
		
		$errno = isset($errorConversion[$errno]) ? $errorConversion[$errno] : $errno;
		if(($pos = strpos($errstr, "\n")) !== false){
			$errstr = substr($errstr, 0, $pos);
		}

		var_dump("An $errno error happened: \"$errstr\" in \"$errfile\" at line $errline");
		
		return true;
	}
	
	private function sortData($data){
		$result = str_repeat("\x00", 4096);
		if($data !== $result){
			$i = 0;
			for($x = 0; $x < 16; ++$x){
				$zM = $x + 256;
				for($z = $x; $z < $zM; $z += 16){
					$yM = $z + 4096;
					for($y = $z; $y < $yM; $y += 256){
						$result{$i} = $data{$y};
						++$i;
					}
				}
			}
		}
		return $result;
	}
	
	private function sortHalfData($data){
		$result = str_repeat("\x00", 2048);
		if($data !== $result){
			$i = 0;
			for($x = 0; $x < 8; ++$x){
				for($z = 0; $z < 16; ++$z){
					$zx = (($z << 3) | $x);
					for($y = 0; $y < 8; ++$y){
						$j = (($y << 8) | $zx);
						$j80 = ($j | 0x80);
						$i1 = ord($data{$j});
						$i2 = ord($data{$j80});
						$result{$i} = chr(($i2 << 4) | ($i1 & 0x0f));
						$result{$i | 0x80} = chr(($i1 >> 4) | ($i2 & 0xf0));
						$i++;
					}
				}
				$i += 128;
			}
		}
		return $result;
	}
}
