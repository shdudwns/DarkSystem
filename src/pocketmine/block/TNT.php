<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\Player;
use pocketmine\utils\Random;

class TNT extends Solid{

	protected $id = self::TNT;

	public function __construct(){

	}

	public function getName(){
		return "TNT";
	}

	public function getHardness(){
		return 0;
	}

	public function canBeActivated(){
		return true;
	}

	public function onActivate(Item $item, Player $player = null){
		if($item->getId() === Item::FLINT_STEEL && $player->isOp()){
			$item->useOn($this);
			$this->getLevel()->setBlock($this, new Air());

			$mot = (new Random())->nextSignedFloat() * M_PI * 2;
			$tnt = Entity::createEntity("Boat", $this->getLevel(), new Compound("", [
				"Pos" => new Enum("Pos", [
					new DoubleTag("", $this->x + 0.5),
					new DoubleTag("", $this->y),
					new DoubleTag("", $this->z + 0.5)
				]),
				"Motion" => new Enum("Motion", [
					new DoubleTag("", -sin($mot) * 0.02),
					new DoubleTag("", 0.2),
					new DoubleTag("", -cos($mot) * 0.02)
				]),
				"Rotation" => new Enum("Rotation", [
					new FloatTag("", 0),
					new FloatTag("", 0)
				]),
				"Fuse" => new ByteTag("Fuse", 80)
			]));
			
			$tnt->spawnToAll();

			return true;
		}

		return false;
	}
}