<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\level;

use pocketmine\level\format\FullChunk;

class SimpleChunkManager implements ChunkManager{
	
	protected $chunks = [];

	protected $seed;
	protected $yMask;
	protected $maxY;

	public function __construct($seed, $yMask, $maxY){
		$this->seed = $seed;
		$this->yMask = $yMask;
		$this->maxY = $maxY;
	}
	
	public function getBlockIdAt($x, $y, $z){
		if($chunk = $this->getChunk($x >> 4, $z >> 4)){
			return $chunk->getBlockId($x & 0xf, $y & $this->getYMask(), $z & 0xf);
		}
		return 0;
	}
	
	public function setBlockIdAt($x, $y, $z, $id){
		if($chunk = $this->getChunk($x >> 4, $z >> 4)){
			$chunk->setBlockId($x & 0xf, $y & $this->getYMask(), $z & 0xf, $id);
		}
	}
	
	public function getBlockDataAt($x, $y, $z) {
		if($chunk = $this->getChunk($x >> 4, $z >> 4)){
			return $chunk->getBlockData($x & 0xf, $y & $this->getYMask(), $z & 0xf);
		}
		return 0;
	}
	
	public function setBlockDataAt($x, $y, $z, $data){
		if($chunk = $this->getChunk($x >> 4, $z >> 4)){
			$chunk->setBlockData($x & 0xf, $y & $this->getYMask(), $z & 0xf, $data);
		}
	}
	
	public function getChunk($chunkX, $chunkZ){
		return isset($this->chunks[$index = Level::chunkHash($chunkX, $chunkZ)]) ? $this->chunks[$index] : null;
	}
	
	public function setChunk($chunkX, $chunkZ, FullChunk $chunk = null){
		if($chunk === null){
			unset($this->chunks[Level::chunkHash($chunkX, $chunkZ)]);
			return;
		}
		$this->chunks[Level::chunkHash($chunkX, $chunkZ)] = $chunk;
	}

	public function cleanChunks(){
		$this->chunks = [];
	}
	
	public function getSeed(){
		return $this->seed;
	}
	
	public function getYMask(){
		return $this->yMask;
	}
	
	public function getMaxY(){
		return $this->maxY;
	}
}