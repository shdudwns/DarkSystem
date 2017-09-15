<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace pocketmine\entity;

use pocketmine\level\Level;
use pocketmine\level\format\FullChunk;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\nbt\tag\Compound;
use pocketmine\network\Network;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;

class Arrow extends Projectile{
	
	const NETWORK_ID = 80;
	
	public $width = 0.4; //Default: 0.5
	public $length = 0.4; //This
	public $height = 0.4; //and This
	
	protected $gravity = 0.03;
	protected $drag = 0.01;
	protected $damage = 2;
	protected $isCritical;
	
	public function __construct(Level $level, Compound $nbt, Entity $shootingEntity = null, $critical = false){
		$this->isCritical = (bool) $critical;
		parent::__construct($level, $nbt, $shootingEntity);
	}
	
	public function onUpdate($currentTick){
		if($this->closed){
			return false;
		}
		$hasUpdate = parent::onUpdate($currentTick);
		if(!$this->hadCollision and $this->isCritical){
			/*$this->level->addParticle(new CriticalParticle($this->add(
				$this->width / 2 + mt_rand(-100, 100) / 500,
				$this->height / 2 + mt_rand(-100, 100) / 500,
				$this->width / 2 + mt_rand(-100, 100) / 500)));*/
		}elseif($this->onGround){
			$this->isCritical = false;
		}
		if($this->age > 1200){
			$this->kill();
			$hasUpdate = true;
		} elseif ($this->y < 1) {
			$this->kill();
			$hasUpdate = true;
		}
		return $hasUpdate;
	}
	
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->type = Arrow::NETWORK_ID;
		$pk->eid = $this->getId();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$player->dataPacket($pk);
		
		parent::spawnTo($player);
	}
	
	public function getBoundingBox(){
		$bb = clone parent::getBoundingBox();
		return $bb->expand(1, 1, 1);
	}
}