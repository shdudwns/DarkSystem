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

use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class BeaconDelayedCheckTask extends Task{
	
	private $pos;
	private $levelId;
	
	public function __construct(Vector3 $pos, $levelId){
		$this->pos = $pos;
		$this->levelId = $levelId;
	}
	
	public function onRun($currentTick){
		$level = Server::getInstance()->getLevel($this->levelId);
		if(!Server::getInstance()->isLevelLoaded($level->getName()) || !$level->isChunkLoaded($this->pos->x >> 4, $this->pos->z >> 4)) return;
		$tile = $level->getTile($this->pos);
		if($tile instanceof Beacon){
			$tile->scheduleUpdate();
		}
	}
}