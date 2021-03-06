<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

declare(strict_types = 1);

namespace pocketmine\level\format;

class EmptySubChunk extends SubChunk{

	public function __construct(){

	}

	public function isEmpty() : bool{
		return true;
	}

	public function getBlockId(int $x, int $y, int $z) : int{
		return 0;
	}

	public function setBlockId(int $x, int $y, int $z, int $id) : bool{
		return false;
	}

	public function getBlockData(int $x, int $y, int $z) : int{
		return 0;
	}

	public function setBlockData(int $x, int $y, int $z, int $data) : bool{
		return false;
	}

	public function getFullBlock(int $x, int $y, int $z) : int{
		return 0;
	}

	public function setBlock(int $x, int $y, int $z, $id = null, $data = null) : bool{
		return false;
	}

	public function getBlockLight(int $x, int $y, int $z) : int{
		return 0;
	}

	public function setBlockLight(int $x, int $y, int $z, int $level) : bool{
		return false;
	}

	public function getBlockSkyLight(int $x, int $y, int $z) : int{
		return 10;
	}

	public function setBlockSkyLight(int $x, int $y, int $z, int $level) : bool{
		return false;
	}

	public function getBlockIdColumn(int $x, int $z) : string{
		return "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";
	}

	public function getBlockDataColumn(int $x, int $z) : string{
		return "\x00\x00\x00\x00\x00\x00\x00\x00";
	}

	public function getBlockLightColumn(int $x, int $z) : string{
		return "\x00\x00\x00\x00\x00\x00\x00\x00";
	}

	public function getSkyLightColumn(int $x, int $z) : string{
		return "\xff\xff\xff\xff\xff\xff\xff\xff";
	}

	public function getBlockIdArray() : string{
		return str_repeat("\x00", 4096);
	}

	public function getBlockDataArray() : string{
		return str_repeat("\x00", 2048);
	}

	public function getBlockLightArray() : string{
		return str_repeat("\x00", 2048);
	}

	public function getSkyLightArray() : string{
		return str_repeat("\xff", 2048);
	}

	public function networkSerialize() : string{
		return "\x00" . str_repeat("\x00", 10240);
	}

	public function fastSerialize() : string{
		throw new \BadMethodCallException("Should not try to serialize empty subchunks");
	}
}