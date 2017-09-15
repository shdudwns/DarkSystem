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

use pocketmine\item\Tool;
use pocketmine\item\Item;


class SeaLanternBlock extends Solid{

	protected $id = self::SEA_LANTERN_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getLightLevel(){
		return 10;
	}

	public function getName() : string{
        return "Sea Lantern Block";
	}

	public function getHardness(){
		return 0.5;
	}

	public function getDrops(Item $item) : array {
		return [
			[Item::PRISMARINE_CRYSTALS, 0, 2],
		];
	}

}