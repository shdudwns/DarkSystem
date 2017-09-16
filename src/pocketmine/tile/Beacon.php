<?php

namespace pocketmine\tile;

use pocketmine\inventory\BeaconInventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\level\Level;
use pocketmine\level\format\FullChunk;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;

class Beacon extends Spawnable implements Nameable, InventoryHolder{
	
	private $inventory;
	
	public function __construct(Level $level, Compound $nbt){
		if(!isset($nbt->primary)){
			$nbt->primary = new IntTag("primary", 0);
		}
		if(!isset($nbt->secondary)){
			$nbt->secondary = new IntTag("secondary", 0);
		}
		$this->inventory = new BeaconInventory($this);
		parent::__construct($level, $nbt);
	}
	
	public function saveNBT(){
		parent::saveNBT();
	}
	
	public function getSpawnCompound(){
		$c = new Compound("", [
			new StringTag("id", Tile::BEACON),
			new ByteTag("isMovable", true),
			new IntTag("x", (int)$this->x),
			new IntTag("y", (int)$this->y),
			new IntTag("z", (int)$this->z)
		]);
		if ($this->hasName()) {
			$c->CustomName = $this->namedtag->CustomName;
		}
		return $c;
	}
	
	public function getName(){
		return $this->hasName() ? $this->namedtag->CustomName->getValue() : "Beacon";
	}
	
	public function hasName(){
		return isset($this->namedtag->CustomName);
	}
	
	public function setName($str){
		if ($str === "") {
			unset($this->namedtag->CustomName);
			return;
		}
		$this->namedtag->CustomName = new StringTag("CustomName", $str);
	}
	
	public function getInventory(){
		return $this->inventory;
	}
}
