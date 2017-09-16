<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\level\format\anvil;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\ByteArray;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\IntArray;
use pocketmine\nbt\tag\LongTag;
use pocketmine\utils\Binary;
use pocketmine\utils\MainLogger;
use pocketmine\level\format\LevelProvider;
use pocketmine\level\format\mcregion\RegionLoader as RegionLoaderMCR;

class RegionLoader extends RegionLoaderMCR{

	public function __construct(LevelProvider $level, $regionX, $regionZ){
		$this->x = $regionX;
		$this->z = $regionZ;
		$this->levelProvider = $level;
		$this->filePath = $this->levelProvider->getPath() . "region/r.$regionX.$regionZ.mca";
		$exists = file_exists($this->filePath);
		touch($this->filePath);
		$this->filePointer = fopen($this->filePath, "r+b");
		stream_set_read_buffer($this->filePointer, 1024 * 16);
		stream_set_write_buffer($this->filePointer, 1024 * 16);
		if(!$exists){
			$this->createBlank();
		}else{
			$this->loadLocationTable();
		}

		$this->lastUsed = time();
	}
	
	public function readChunk($x, $z){
		$index = self::getChunkOffset($x, $z);
		if($index < 0 or $index >= 4096){
			return null;
		}

		$this->lastUsed = time();

		if(!$this->isChunkGenerated($index)){
			return null;
		}

		fseek($this->filePointer, $this->locationTable[$index][0] << 12);
		$length = Binary::readInt(fread($this->filePointer, 4));
		$compression = ord(fgetc($this->filePointer));

		if($length <= 0 or $length >= self::MAX_SECTOR_LENGTH){
			if($length >= self::MAX_SECTOR_LENGTH){
				$this->locationTable[$index][0] = ++$this->lastSector;
				$this->locationTable[$index][1] = 1;
				MainLogger::getLogger()->error("Corrupted chunk header detected");
			}
			
			$this->generateChunk($x, $z);
			fseek($this->filePointer, $this->locationTable[$index][0] << 12);
			$length = Binary::readInt(fread($this->filePointer, 4));
			$compression = ord(fgetc($this->filePointer));
		}

		if($length > ($this->locationTable[$index][1] << 12)){
			MainLogger::getLogger()->error("Corrupted bigger chunk detected");
			$this->locationTable[$index][1] = $length >> 12;
			$this->writeLocationIndex($index);
		}elseif($compression !== self::COMPRESSION_ZLIB and $compression !== self::COMPRESSION_GZIP){
			MainLogger::getLogger()->error("Invalid compression type");
			return null;
		}

		$chunk = Chunk::fromBinary(fread($this->filePointer, $length - 1), $this->levelProvider);
		if($chunk instanceof Chunk){
			return $chunk;
		}else{
			return null;
		}
	}

	public function generateChunk($x, $z){
		$levelProvider = $this->levelProvider;
		$nbt = new Compound("Level", []);
		$nbt->xPos = new IntTag("xPos", ($this->getX() * 32) + $x);
		$nbt->zPos = new IntTag("zPos", ($this->getZ() * 32) + $z);
		$nbt->LastUpdate = new LongTag("LastUpdate", 0);
		$nbt->LightPopulated = new ByteTag("LightPopulated", 0);
		$nbt->TerrainPopulated = new ByteTag("TerrainPopulated", 0);
		$nbt->V = new ByteTag("V", self::VERSION);
		$nbt->InhabitedTime = new LongTag("InhabitedTime", 0);
		$nbt->Biomes = new ByteArray("Biomes", str_repeat(chr(-1), 256));
		$nbt->BiomeColors = new IntArray("BiomeColors", array_fill(0, 256, Binary::readInt("\x00\x85\xb2\x4a")));
		$nbt->HeightMap = new IntArray("HeightMap", array_fill(0, 256, $levelProvider::getMaxY() - 1));
		$nbt->Sections = new Enum("Sections", []);
		$nbt->Sections->setTagType(NBT::TAG_Compound);
		$nbt->Entities = new Enum("Entities", []);
		$nbt->Entities->setTagType(NBT::TAG_Compound);
		$nbt->TileEntities = new Enum("TileEntities", []);
		$nbt->TileEntities->setTagType(NBT::TAG_Compound);
		$nbt->TileTicks = new Enum("TileTicks", []);
		$nbt->TileTicks->setTagType(NBT::TAG_Compound);
		$writer = new NBT(NBT::BIG_ENDIAN);
		$nbt->setName("Level");
		$writer->setData(new Compound("", ["Level" => $nbt]));
		$chunkData = $writer->writeCompressed(ZLIB_ENCODING_DEFLATE, RegionLoader::$COMPRESSION_LEVEL);
		$this->saveChunk($x, $z, $chunkData);
	}

}
