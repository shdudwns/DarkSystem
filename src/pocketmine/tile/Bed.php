<?php

namespace pocketmine\tile;

use pocketmine\level\Level;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\ByteTag;

class Bed extends Spawnable{
	
	public function __construct(Level $level, Compound $nbt){
		if(!isset($nbt->color) or !($nbt->color instanceof ByteTag)){
			$nbt->color = new ByteTag("color", 14);
		}
		
		parent::__construct($level, $nbt);
	}
	
	public function getColor(){
		return $this->namedtag->color->getValue();
	}
	
	public function setColor($color){
		$this->namedtag["color"] = $color & 0x0f;
		$this->onChanged();
	}
	
	public function getSpawnCompound(){
		return new Compound("", [
			new StringTag("id", Tile::BED),
			new IntTag("x", (int) $this->x),
			new IntTag("y", (int) $this->y),
			new IntTag("z", (int) $this->z),
			new ByteTag("color", (int) $this->namedtag["color"]),
			new ByteTag("isMovable", (int) $this->namedtag["isMovable"])
		]);
	}
}
