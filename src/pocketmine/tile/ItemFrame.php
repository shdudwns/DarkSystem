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
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\StringTag;

class ItemFrame extends Spawnable{

	public function __construct(Level $level, Compound $nbt){
		if(!isset($nbt->ItemRotation)){
			$nbt->ItemRotation = new ByteTag("ItemRotation", 0);
		}

		if(!isset($nbt->ItemDropChance)){
			$nbt->ItemDropChance = new FloatTag("ItemDropChance", 1.0);
		}

		parent::__construct($level, $nbt);
	}

	public function hasItem(){
		return $this->getItem()->getId() !== Item::AIR;
	}

	public function getItem(){
		if(isset($this->namedtag->Item)){
			return NBT::getItemHelper($this->namedtag->Item);
		}else{
			return Item::get(Item::AIR);
		}
	}

	public function setItem(Item $item = null){
		if($item !== null and $item->getId() !== Item::AIR){
			$this->namedtag->Item = NBT::putItemHelper(-1, "Item");
		}else{
			unset($this->namedtag->Item);
		}
		
		$this->onChanged();
	}

	public function getItemRotation(){
		return $this->namedtag->ItemRotation->getValue();
	}

	public function setItemRotation($rotation){
		$this->namedtag->ItemRotation->setValue($rotation);
		$this->onChanged();
	}

	public function getItemDropChance(){
		return $this->namedtag->ItemDropChance->getValue();
	}

	public function setItemDropChance($chance){
		$this->namedtag->ItemDropChance->setValue($chance);
		$this->onChanged();
	}

	public function addAdditionalSpawnData(Compound $nbt){
		$nbt->ItemDropChance = $this->namedtag->ItemDropChance;
		$nbt->ItemRotation = $this->namedtag->ItemRotation;
		if($this->hasItem()){
			$nbt->Item = $this->namedtag->Item;
		}
	}
	
	public function getSpawnCompound(){
		$tag = new Compound("", [
			new StringTag("id", Tile::ITEM_FRAME),
			new IntTag("x", (int) $this->x),
			new IntTag("y", (int) $this->y),
			new IntTag("z", (int) $this->z),
			$this->namedtag->ItemDropChance,
			$this->namedtag->ItemRotation,
		]);
		if($this->hasItem()){
			$tag->Item = $this->namedtag->Item;
		}
		
		return $tag;
	}
}