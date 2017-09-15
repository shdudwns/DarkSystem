<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\tile;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\format\FullChunk;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;

class FlowerPot extends Spawnable{
	
	public function __construct(Level $level, Compound $nbt){
		if(!isset($nbt->Item)){
			$nbt->item = new ShortTag("item", 0);
		}
		if(!isset($nbt->Data)){
			$nbt->mData = new IntTag("mData", 0);
		}
		parent::__construct($level, $nbt);
	}
	
	public function canAddItem(Item $item){
		if(!$this->isEmpty()){
			return false;
		}
		switch($item->getId()){
			case Item::TALL_GRASS:
				if($item->getDamage() === 1){
					return false;
				}
			case Item::SAPLING:
			case Item::DEAD_BUSH:
			case Item::DANDELION:
			case Item::RED_FLOWER:
			case Item::BROWN_MUSHROOM:
			case Item::RED_MUSHROOM:
			case Item::CACTUS:
				return true;
			default:
				return false;
		}
	}
	
	public function getItem(){
		return Item::get((int) ($this->namedtag["item"] ?? 0), (int) ($this->namedtag["mData"] ?? 0), 1);
	}
	
	public function setItem(Item $item){
		$this->namedtag["item"] = $item->getId();
		$this->namedtag["mData"] = $item->getDamage();
		$this->spawnToAll();
		if($this->chunk){
			$this->chunk->setChanged();
		}
	}
	
	public function removeItem(){
		$this->setItem(Item::get(Item::AIR));
	}
	
	public function isEmpty(){
		return $this->getItem()->getId() === Item::AIR;
	}
	
	public function getSpawnCompound(){
		return new Compound("", [
			new StringTag("id", Tile::FLOWER_POT),
			new IntTag("x", (int) $this->x),
			new IntTag("y", (int) $this->y),
			new IntTag("z", (int) $this->z),
			new ShortTag("item", (int) $this->namedtag["item"]),
			new IntTag("mData", (int) $this->namedtag["mData"])
		]);
	}
}