<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\level\format;

class SubChunk{

	protected $ids;
	protected $data;
	protected $blockLight;
	protected $skyLight;

	private static function assignData(&$target, $data, $length, $value = "\x00"){
		if(strlen($data) !== $length){
			assert($data === "", "Invalid non-zero length given, expected $length, got " . strlen($data));
			$target = str_repeat($value, $length);
		}else{
			$target = $data;
		}
	}

	public function __construct(string $ids = "", string $data = "", string $skyLight = "", string $blockLight = ""){
		self::assignData($this->ids, $ids, 4096);
		self::assignData($this->data, $data, 2048);
		self::assignData($this->skyLight, $skyLight, 2048, "\xff");
		self::assignData($this->blockLight, $blockLight, 2048);
	}

	public function isEmpty(){
		return (
            substr_count($this->ids, "\x00") === 4096 and
            substr_count($this->skyLight, "\xff") === 2048 and
            substr_count($this->blockLight, "\x00") === 2048
    );
	}

	public function getBlockId($x, $y, $z){
		return ord($this->ids{($x << 8) | ($z << 4) | $y});
	}

	public function setBlockId($x, $y, $z, $id){
		$this->ids{($x << 8) | ($z << 4) | $y} = chr($id);
		return true;
	}

	public function getBlockData($x, $y, $z){
		$m = ord($this->data{($x << 7) + ($z << 3) + ($y >> 1)});
		if(($y & 1) === 0){
			return $m & 0x0f;
		}else{
			return $m >> 4;
		}
	}

	public function setBlockData($x, $y, $z, $data){
		$i = ($x << 7) | ($z << 3) | ($y >> 1);
		if(($y & 1) === 0){
			$this->data{$i} = chr((ord($this->data{$i}) & 0xf0) | ($data & 0x0f));
		}else{
			$this->data{$i} = chr((($data & 0x0f) << 4) | (ord($this->data{$i}) & 0x0f));
		}
		return true;
	}

	public function getFullBlock($x, $y, $z){
		$i = ($x << 8) | ($z << 4) | $y;
		if(($y & 1) === 0){
			return (ord($this->ids{$i}) << 4) | (ord($this->data{$i >> 1}) & 0x0f);
		}else{
			return (ord($this->ids{$i}) << 4) | (ord($this->data{$i >> 1}) >> 4);
		}
	}

	public function setBlock($x, $y, $z, $id = null, $data = null){
		$i = ($x << 8) | ($z << 4) | $y;
		$changed = false;
		if($id !== null){
			$block = chr($id);
			if($this->ids{$i} !== $block){
				$this->ids{$i} = $block;
				$changed = true;
			}
		}

		if($data !== null){
			$i >>= 1;
			$byte = ord($this->data{$i});
			if(($y & 1) === 0){
				$this->data{$i} = chr(($byte & 0xf0) | ($data & 0x0f));
			}else{
				$this->data{$i} = chr((($data & 0x0f) << 4) | ($byte & 0x0f));
			}
			if($this->data{$i} !== $byte){
				$changed = true;
			}
		}

		return $changed;
	}

	public function getBlockLight($x, $y, $z){
		$byte = ord($this->blockLight{($x << 7) + ($z << 3) + ($y >> 1)});
		if(($y & 1) === 0){
			return $byte & 0x0f;
		}else{
			return $byte >> 4;
		}
	}

	public function setBlockLight($x, $y, $z, $level){
		$i = ($x << 7) + ($z << 3) + ($y >> 1);
		$byte = ord($this->blockLight{$i});
		if(($y & 1) === 0){
			$this->blockLight{$i} = chr(($byte & 0xf0) | ($level & 0x0f));
		}else{
			$this->blockLight{$i} = chr((($level & 0x0f) << 4) | ($byte & 0x0f));
		}
		return true;
	}

	public function getBlockSkyLight($x, $y, $z){
		$byte = ord($this->skyLight{($x << 7) + ($z << 3) + ($y >> 1)});
		if(($y & 1) === 0){
			return $byte & 0x0f;
		}else{
			return $byte >> 4;
		}
	}

	public function setBlockSkyLight($x, $y, $z, $level){
		$i = ($x << 7) + ($z << 3) + ($y >> 1);
		$byte = ord($this->skyLight{$i});
		if(($y & 1) === 0){
			$this->skyLight{$i} = chr(($byte & 0xf0) | ($level & 0x0f));
		}else{
			$this->skyLight{$i} = chr((($level & 0x0f) << 4) | ($byte & 0x0f));
		}
		return true;
	}

	public function getHighestBlockAt($x, $z){
		for($y = 15; $y >= 0; --$y){
			if($this->ids{($x << 8) | ($z << 4) | $y} !== "\x00"){
				return $y;
			}
		}

		return -1;
	}

	public function getBlockIdColumn($x, $z){
		return substr($this->ids, (($x << 8) | ($z << 4)), 16);
	}

	public function getBlockDataColumn($x, $z){
		return substr($this->data, (($x << 7) | ($z << 3)), 8);
	}

	public function getBlockLightColumn($x, $z){
		return substr($this->blockLight, (($x << 7) | ($z << 3)), 8);
	}

	public function getSkyLightColumn($x, $z){
		return substr($this->skyLight, (($x << 7) | ($z << 3)), 8);
	}

	public function getBlockIdArray(){
		assert(strlen($this->ids) === 4096, "Wrong length of ID array, expecting 4096 bytes, got " . strlen($this->ids));
		return $this->ids;
	}

	public function getBlockDataArray(){
		assert(strlen($this->data) === 2048, "Wrong length of data array, expecting 2048 bytes, got " . strlen($this->data));
		return $this->data;
	}

	public function getSkyLightArray(){
		assert(strlen($this->skyLight) === 2048, "Wrong length of skylight array, expecting 2048 bytes, got " . strlen($this->skyLight));
		return $this->skyLight;
	}

	public function getBlockLightArray(){
		assert(strlen($this->blockLight) === 2048, "Wrong length of light array, expecting 2048 bytes, got " . strlen($this->blockLight));
		return $this->blockLight;
	}

	public function networkSerialize(){
		return "\x00" . $this->ids . $this->data . $this->skyLight . $this->blockLight;
	}

	public function fastSerialize(){
		return
			$this->ids .
			$this->data .
			$this->skyLight .
			$this->blockLight;
	}

	public static function fastDeserialize($data){
		return new SubChunk(
			substr($data,    0, 4096),
			substr($data, 4096, 2048),
			substr($data, 6144, 2048),
			substr($data, 8192, 2048)
		);
	}
}