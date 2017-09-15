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

use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\item\Tool;

class BeaconBlock extends Solid{

	protected $id = self::BEACON_BLOCK;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getLightLevel(){
		return 10;
	}

	public function getHardness(){
		return 3;
	}

	public function getName() : string{
        return "Beacon Block";
	}

	/*public function onActivate(Item $item, Player $player = null){
		if($player instanceof Player){
			$player->addWindow(new BeaconInventory($this));
		}

		return true;
	}*/

}