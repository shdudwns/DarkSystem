<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\level\Location;
use pocketmine\Player;
use pocketmine\network\protocol\AnimatePacket;
use pocketmine\math\Vector3;

class PlayerMoveEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;

	private $from;
	private $to;

	public function __construct(Player $player, Location $from, Location $to){
		$this->player = $player;
		$this->from = $from;
		$this->to = $to;
		if($player->getDataFlag($player::DATA_PLAYER_FLAGS, $player::DATA_PLAYER_FLAG_SLEEP)){
			$block = $from->level->getBlock(new Vector3(floor($from->getX()), ceil($from->getY()), floor($from->getZ())));
			$blockUp = $from->level->getBlock(new Vector3(floor($from->getX()), ceil($from->getY()+1), floor($from->getZ())));
			if($block->getId() != 26 && $blockUp->getId() != 26){
				$player->stopSleep();
			}
		}
	}

	public function getFrom(){
		return $this->from;
	}

	public function setFrom(Location $from){
		$this->from = $from;
	}

	public function getTo(){
		return $this->to;
	}

	public function setTo(Location $to){
		$this->to = $to;
	}
}