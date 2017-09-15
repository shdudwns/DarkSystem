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

use raklib\protocol\EncapsulatedPacket;
use raklib\RakLib;
use pocketmine\network\CachedEncapsulatedPacket;
use pocketmine\network\protocol\DataPacket;
use pocketmine\utils\Binary;
use pocketmine\network\protocol\BatchPacket;
use pocketmine\network\protocol\MoveEntityPacket;
use pocketmine\network\protocol\SetEntityMotionPacket;
use pocketmine\network\protocol\MovePlayerPacket;

class PacketManager extends Worker{
	
	protected $classLoader;
	protected $shutdown;
	
	protected $externalQueue;
	protected $internalQueue;	

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
		register_shutdown_function([$this, "shutdownHandler"]);
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
			$this->tick();
			$time = microtime(true) - $start;
			if($time < 0.024){
				@time_sleep_until(microtime(true) + 0.025 - $time);
			}
		}
	}

	protected function tick(){				
		while(count($this->internalQueue) > 0){
			$data = unserialize($this->readMainToThreadPacket());
			$this->checkPacket($data);
		}
	}
	
	protected function checkPacket($data){
		if(isset($data['moveData'])){
			foreach($data['moveData'] as $identifier => $moveData){
				$moveStr = "";
				foreach($moveData['data'] as $singleMoveData){
					if($singleMoveData[7]){
						$pk = new MovePlayerPacket();
						$pk->eid = $singleMoveData[0];
						$pk->x = $singleMoveData[1];
						$pk->y = $singleMoveData[2];
						$pk->z = $singleMoveData[3];
						$pk->pitch = $singleMoveData[6];
						$pk->yaw = $singleMoveData[5];
						$pk->bodyYaw = $singleMoveData[4];
					} else {
						$pk = new MoveEntityPacket();
						$pk->entities = [$singleMoveData];
					}
					$pk->encode($moveData['playerProtocol']);
					$moveStr .= Binary::writeVarInt(strlen($pk->buffer)) . $pk->buffer;					
				}
				$buffer = zlib_encode($moveStr, ZLIB_ENCODING_DEFLATE, 7);
				$pkBatch = new BatchPacket();
				$pkBatch->payload = $buffer;
				$pkBatch->encode($moveData['playerProtocol']);
				$pkBatch->isEncoded = true;
				$this->externalQueue[] = $this->makeBuffer($identifier, $pkBatch, false, false);
			}	
			foreach($data['motionData'] as $identifier => $motionData){
				$motionStr = "";
				foreach($motionData['data'] as $singleMotionData){
					$pk = new SetEntityMotionPacket();
					$pk->entities = [$singleMotionData];
					$pk->encode($motionData['playerProtocol']);
					$motionStr .= Binary::writeVarInt(strlen($pk->buffer)) . $pk->buffer;		
				}
				$buffer = zlib_encode($motionStr, ZLIB_ENCODING_DEFLATE, 7);
				$pkBatch = new BatchPacket();
				$pkBatch->payload = $buffer;
				$pkBatch->encode($motionData['playerProtocol']);
				$pkBatch->isEncoded = true;
				$this->externalQueue[] = $this->makeBuffer($identifier, $pkBatch, false, false);
			}
		} elseif($data['isBatch']){
			$packetsStr = [];
			foreach($data['packets'] as $protocol => $packetData){		
				foreach($packetData as $p){
					if(!isset($packetsStr[$protocol])){
						$packetsStr[$protocol] = "";
					}
					$packetsStr[$protocol] .= Binary::writeVarInt(strlen($p)) . $p;
				}
			}
			
			$packs = [];
			foreach($packetsStr as $protocol => $str){
				$buffer = zlib_encode($str, ZLIB_ENCODING_DEFLATE, $data['networkCompressionLevel']);
				$pk = new BatchPacket();
				$pk->payload = $buffer;
				$pk->encode($protocol);
				$pk->isEncoded = true;
				$packs[$protocol] = $pk;
			}
			
			foreach($data['targets'] as $target){
				if(isset($packs[$target[1]])){
					$this->externalQueue[] = $this->makeBuffer($target[0], $packs[$target[1]], false, false);
				}
			}
		}
	}
	
	protected function makeBuffer($identifier, $fullPacket, $needACK, $identifierACK){		
		$data = array(
			'identifier' => $identifier,
			'buffer' => $fullPacket->buffer
		);
		return serialize($data);
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
		
		return true;
	}
	
	public function shutdownHandler(){
		if($this->shutdown !== true){
			var_dump("Paketler Çöktü!");
		}
	}

}
