<?php

namespace pocketmine\darkbot\entity;

use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\PlayerListPacket;
use pocketmine\entity\animal\WalkingAnimal;

class DarkBotAI3 extends Human{
	
	//protected $moveTime = 0;
	
	//protected $sprintTime = 0;
	
	//protected $speed = 1;
	
    public function getSaveId(){
        return "Human";
    }
    
    public function initEntity(){
		parent::initEntity();

		$this->setMaxHealth(20);
	}
	
	/*public function isKnockback(){
		return $this->attackTime > 0;
	}*/
	
   public function spawnTo(Player $player){
	   if($player !== $this and !isset($this->hasSpawned[$player->getId()])){
			$pk = new PlayerListPacket();
			$pk->type = PlayerListPacket::TYPE_ADD;
			$pk->entries[] = [$this->getUniqueId(), $this->getId(), $this->getName(), $this->skinName, $this->skin, $this->skinGeometryName, $this->skinGeometryData, $this->capeData];
					
			/*$pk2 = new PlayerListPacket();
			$pk2->type = PlayerListPacket::TYPE_REMOVE;
			$pk2->entries[] = [$this->getUniqueId()];*/
			
			$this->hasSpawned[$player->getId()] = $player;			
			$pk3 = new AddPlayerPacket();
			$pk3->uuid = $this->getUniqueId();
			$pk3->username = $this->getName();
			$pk3->eid = $this->getId();
			//$pk3->x = $this->x;
			//$pk3->y = $this->y;
			//$pk3->z = $this->z;
			$pk3->x = $this->server->getDefaultLevel()->getSafeSpawn()->x;
			$pk3->y = $this->server->getDefaultLevel()->getSafeSpawn()->y;
			$pk3->z = $this->server->getDefaultLevel()->getSafeSpawn()->z;
			$pk3->speedX = $this->motionX;
			$pk3->speedY = $this->motionY;
			$pk3->speedZ = $this->motionZ;
			$pk3->yaw = $this->yaw;
			$pk3->pitch = $this->pitch;
			$pk3->item = $this->getInventory()->getItemInHand();
			$pk3->metadata = $this->dataProperties;
			
			//$this->server->batchPackets([$player], [$pk, $pk3, $pk2]);
			$this->server->batchPackets([$player], [$pk, $pk3]);
			$this->inventory->sendArmorContents($player);	
			$this->inventory->sendHeldItem($player);
		}		
	}
	
	/*public function onUpdate($currentTick){
		if($this->closed){
			return false;
		}
		$tickDiff = $currentTick - $this->lastUpdate;
		$this->lastUpdate = $currentTick;
		$this->entityBaseTick($tickDiff);
		$this->move($dx, $dy, $dz);
		return true;
	}
	
	public function updateMovement(){
		if(
			$this->lastX !== $this->x
			|| $this->lastY !== $this->y
			|| $this->lastZ !== $this->z
			|| $this->lastYaw !== $this->yaw
			|| $this->lastPitch !== $this->pitch
		){
			$this->lastX = $this->x;
			$this->lastY = $this->y;
			$this->lastZ = $this->z;
			$this->lastYaw = $this->yaw;
			$this->lastPitch = $this->pitch;
		}
		$this->level->addEntityMovement($this->getViewers(), $this->id, $this->x, $this->y, $this->z, $this->yaw, $this->pitch);
	}
	
	public function entityBaseTick($tickDiff = 1){
		$hasUpdate = Entity::entityBaseTick($tickDiff);
		if($this->isInsideOfSolid()){
			$hasUpdate = true;
			$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_SUFFOCATION, 1);
			$this->attack($ev->getFinalDamage(), $ev);
		}
		if($this->moveTime > 0){
			$this->moveTime -= $tickDiff;
		}
		 if($this->sprintTime > 0){
			$this->sprintTime -= $tickDiff;
		}
		if($this->attackTime > 0){
			$this->attackTime -= $tickDiff;
		}
		return $hasUpdate;
	}

	public function move($dx, $dy, $dz){
		$list = $this->level->getCollisionCubes($this, $this->level->getServer()->getTick() > 1 ? $this->boundingBox->getOffsetBoundingBox($dx, $dy, $dz) : $this->boundingBox->addCoord($dx, $dy, $dz));
		foreach($list as $bb){
			$dx = $bb->calculateXOffset($this->boundingBox, $dx);
		}
		$this->boundingBox->offset($dx, 0, 0);
		foreach($list as $bb){
			$dz = $bb->calculateZOffset($this->boundingBox, $dz);
		}
		$this->boundingBox->offset(0, 0, $dz);
	    foreach($list as $bb){
		    $dy = $bb->calculateYOffset($this->boundingBox, $dy);
	    }
	    $this->boundingBox->offset(0, $dy, 0);
	    $this->setComponents($this->x + $dx, $this->y + $dy, $this->z + $dz);
		$this->checkChunks();
		return true;
	}*/
	
	/*public function attack($damage, EntityDamageEvent $source){
		
	}*/
	
	public function getSpeed(){
		return 1;
	}
	
	public function getDrops(){
		return [];
	}
}
