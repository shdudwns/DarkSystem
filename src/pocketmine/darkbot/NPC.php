<?php

namespace pocketmine\darkbot\entity;

use pocketmine\Player;
use pocketmine\utils\UUID;
use pocketmine\entity\Creature;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\level\Position;
use pocketmine\level\Level;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\math\AxisAlignedBB;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\MovePlayerPacket;
use pocketmine\network\protocol\MoveEntityPacket;
use pocketmine\network\protocol\SetEntityMotionPacket;
use pocketmine\scheduler\CallbackTask;

class NPC extends Human{
	
	public $networkId = 63;
	public $target;
	public $spawnPos;
	public $attackDamage = 1;
	public $attackRate = 10;
	public $attackDelay = 0;
	public $speed;
	public $drops;
	public $skin;
	public $heldItem;
	public $range;
    public $width = 0.6;
	public $length = 0.6;
	public $height = 1.8;
	public $eyeHeight = 1.62;
	public $stepHeight = 0.5;
	public $knockback = 0;
	public $knockbackTicks = 0;
	public $a;
	
	public static $jump = 2.5;
	public static $attack = 1.5;
	
	const NETWORK_ID = 63;
	
	public function __construct($level, $nbt){
		parent::__construct($level, $nbt);
		
		$this->networkId = 63;
		$this->range = $this->namedtag["range"];
		$this->spawnPos = new Position($this->namedtag["spawnPos"][0],$this->namedtag["spawnPos"][1],$this->namedtag["spawnPos"][2],$this->level);
		$this->attackDamage = $this->namedtag["attackDamage"];
		$this->speed = $this->namedtag["speed"];
		$this->skin = $this->namedtag["skin"];
		$this->heldItem = new Item(0, 0, 0);
        $this->npc = "true";
        $this->type = $this->namedtag["type"];
        $this->a = 10;
	}
	
	public function initEntity(){
        parent::initEntity();
        $this->dataProperties[self::DATA_NO_AI] = [self::DATA_TYPE_BYTE, 1];
		if(isset($this->namedtag->maxHealth)){
			parent::setMaxHealth($this->namedtag["maxHealth"]);
			$this->setHealth($this->namedtag["maxHealth"]);
		}else{
			$this->setMaxHealth(20);
			$this->setHealth(20);
		}
    }
    
    public function getSaveId(){
        return "DarkBot";
    }
    
	 public function getName(){
		return $this->getNameTag();
	}

   public function getMaxHealth(){
		return $this->namedtag["maxHealth"];
	}
	
   public function setMaxHealth($health){
		$this->namedtag->maxHealth = new IntTag("maxHealth",$health);
		parent::setMaxHealth($health);
  }
  
  public function getSpeed(){
		return $this->speed;
	}
	
	public function spawnTo(Player $player){
		parent::spawnTo($player);
		if($this->networkId === 63){
			$pk = new AddPlayerPacket();
			$pk->uuid = UUID::fromData($this->getId(), $this->skinName, $this->skin, $this->skinGeometryName, $this->skinGeometryData, $this->capeData, $this->getNameTag());
			$pk->username = $this->getName();
			$pk->eid = $this->getId();
			$pk->x = $this->x;
			$pk->y = $this->y + $this->getEyeHeight() - 1.5;
			$pk->z = $this->z;
			$pk->speedX = $this->motionX;
			$pk->speedY = $this->motionY;
			$pk->speedZ = $this->motionZ;
			$pk->yaw = $this->yaw;
			$pk->pitch = $this->pitch;
			$pk->item = $this->heldItem;
			$pk->metadata = $this->dataProperties;
			$player->dataPacket($pk);
			
			$this->inventory->sendArmorContents($player);	
			$this->inventory->sendHeldItem($player);
		}
	}
	
	public function saveNBT(){
        parent::saveNBT();
		$this->namedtag->maxHealth = new IntTag("maxHealth",$this->getMaxHealth());
		$this->namedtag->spawnPos = new Enum("spawnPos", [
            new DoubleTag("", $this->spawnPos->x),
            new DoubleTag("", $this->spawnPos->y),
            new DoubleTag("", $this->spawnPos->z)
        ]);
		$this->namedtag->range = new FloatTag("range",$this->range);
		$this->namedtag->attackDamage = new FloatTag("attackDamage",$this->attackDamage);
		$this->namedtag->networkId = new IntTag("networkId",63);
		$this->namedtag->speed = new FloatTag("speed",$this->speed);
		$this->namedtag->skin = new StringTag("skin",$this->skin);
        $this->namedtag->npc = new StringTag("npc","true");
        $this->namedtag->heldItem= new StringTag("heldItem",$this->heldItem);
        $this->namedtag->type = new StringTag("type",$this->type);
    }
    
    private function findTarget(){
		$lv = $this->getLevel();
		$ps = $lv->getPlayers();
		if(!count($ps)){
			return [null,null];
		}
		$target = null;
		$dist = null;
		foreach($ps as $pl){
			if($pl->isCreative()){
				continue;
			}
			$cd = $this->distance($pl);
			if(($cd > $this->range)||($dist && $cd > $dist)||$pl->getHealth()<1){
				continue;
			}
			$dist = $cd;
			$target = $pl;
		}
		return [$target,$dist];
	}
	
	/*public function onUpdate($currentTick){
		if($this->knockbackTicks > 0) $this->knockbackTicks--;
		if(($player = $this->target) && $player->isAlive()){
			if($this->distanceSquared($this->spawnPos) > $this->range){
				$this->setPosition($this->spawnPos);
				$this->setHealth($this->getMaxHealth());
				$this->target = null;
			}else{
				if(!$this->onGround){
					if($this->motionY > -$this->gravity * 4){
						$this->motionY = -$this->gravity * 4;
					}else{
						$this->motionY -= $this->gravity;
					}
					$this->move($this->motionX, $this->motionY, $this->motionZ);
				}elseif($this->knockbackTicks > 0){
				}else{
					$x = $player-> x - $this->x;
					$y = $player-> y - $this->y;
					$z = $player-> z - $this->z;
					if($x ** 2 + $z ** 2 < 0.7){
						$this->motionX = 0;
						$this->motionZ = 0;
					}else{
						$diff = abs($x) + abs($z);
						$this->motionX = $this->speed * 0.15 * ($x / $diff);
						$this->motionZ = $this->speed * 0.15 * ($z / $diff);
					}
					$this->yaw = rad2deg(atan2(-$x,$z));
					if($this->networkId === 53){
						$this->yaw+=180;
					}
					$this->pitch = rad2deg(atan(-$y));
					$this->move($this->motionX, $this->motionY, $this->motionZ);
					if($this->distanceSquared($this->target) < 1 && $this->attackDelay++ > $this->attackRate){
						$this->attackDelay = 0;
						$ev = new EntityDamageByEntityEvent($this, $this->target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->attackDamage);
						$player->attack($ev->getFinalDamage(), $ev);
					}
				}
			}
		}
		$this->updateMovement();
		//parent::onUpdate($currentTick);
		//return !$this->closed;
	}*/
	
	/*public function onUpdate($currentTick){
        switch($this->type){
        case 1:
        if($this->knockbackTicks > 0) $this->knockbackTicks--;
            $this->a--;
		if(($player = $this->target) && $player->isAlive()){
			if(isset($this->target) and ($this->target ===null)) unset($this->target);
			if($this->distanceSquared($this->spawnPos) > $this->range){
				$this->setPosition($this->spawnPos);
				$this->setHealth($this->getMaxHealth());
				$this->target = null;
			}else{
                $z=$player->z-$this->z;
				$y=$player->y-$this->y;
				$x=$player->x-$this->x;
				$atn = atan2($z, $x);
				$ppos=$player->getPosition();
				  if($this->distance(new Vector3($ppos->getX(),$ppos->getY(),$ppos->getZ())) <= 0.8){
                     if($this->a <= 0){
		                 $this->move($x/8,$y/1.2,$z/8);
		                 $ev = new EntityDamageByEntityEvent($this, $this->target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->attackDamage);
                         $ev->setKnockBack(3);
					     $player->attack($ev->getFinalDamage(), $ev);
                         $this->a = 10;
                         }                                          
					 }else{
				   $this->setRotation(rad2deg($atn -M_PI_2),rad2deg(-atan2($y, sqrt($x ** 2 + $z ** 2))));
                   $this->move($x/8,$y/1.2,$z/8);
          if(mt_rand(0,25) <= 5){
           $this->setSneaking(true);
          }elseif(mt_rand(0,25) > 20){
          $this->setSneaking(false);
      }
     }
     }
   }
      break;
      case 2:
      $x = $this->x;
      $y = $this->y;
      $z = $this->z;
      $a = null;
      if($a = null){
      $a = "b";
      }
      if($this->level->getBlockIdAt($x + 1.2,$y,$z) !== 0){
      $a = "b";
      }elseif($this->level->getBlockIdAt($x-1.2,$y,$z) !== 0){
      $a = "c";
      }elseif($this->level->getBlockIdAt($x,$y,$z-1.2) !== 0){
      $a = "d";
      }elseif($this->level->getBlockIdAt($x,$y,$z+1.2) !== 0){
      $a = "e";
      }elseif($this->level->getBlockIdAt($x+1.2,$y,$z+1.2) !== 0){
      $a = "f";
      }elseif($this->level->getBlockIdAt($x-1.2,$y,$z-1.2) !== 0){
      $a = "g";
      }elseif($this->level->getBlockIdAt($x-1.2,$y,$z+1.2) !== 0){
      $a = "h";
      }elseif($this->level->getBlockIdAt($x+1.2,$y,$z-1.2) !== 0){
      $a = "i";
      }else{
      $a = "b";
}
       switch($a){
       case "b": $this->move(-1/8,0,0); break;
       case "c": $this->move(1/8,0,0); break;
       case  "d": $this->move(0,0,1/8); break;
       case "e": $this->move(0,0,-1/8); break;
       case "f": $this->move(-1/8,0,-1/8); break;
       case "g": $this->move(1/8,0,1/8); break;
       case "h": $this->move(-1/8,0,+1/8); break;
       case "i": $this->move(+1/8,0,-1/8);break;
       case "a": $this->move(1/8,0,0); break;
      }
      break;
   }
		$this->updateMovement();
		//parent::onUpdate($currentTick);
		//return !$this->closed;
	}*/
	
	public function onUpdate($currentTick){
		/*if($this->knockback){
			if(time() < $this->knockback){
				return parent::onUpdate($currentTick);
			}
			$this->knockback = 0;
		}*/
		$hasUpdate = false;
		$this->timings->startTiming();
		$tickDiff = max(1, $currentTick - $this->lastUpdate);
		$bb = clone $this->getBoundingBox();
		$onGround = count($this->level->getCollisionBlocks($bb->offset(0, -$this->gravity, 0))) > 0;
		if(!$onGround){
			$this->motionY -= $this->gravity;
			$this->x += $this->motionX * $tickDiff;
			$this->y += $this->motionY * $tickDiff;
			$this->z += $this->motionZ * $tickDiff;
		}else{
			$this->motionX = 0;
			$this->motionY = 0;
			$this->motionZ = 0;
			if($this->y != floor($this->y)) $this->y = floor($this->y);
			list($target,$dist) = $this->findTarget();
			if($target !== null && $dist > 0){
				$dir = $target->subtract($this);
				$dir = $dir->divide($dist);
				$this->yaw = rad2deg(atan2(-$dir->getX(),$dir->getZ()));
				$this->pitch = rad2deg(atan(-$dir->getY()));
				if($dist > self::$attack){
					$x = $dir->getX() * $this->speed;
					$y = 0;
					$z = $dir->getZ() * $this->speed;
					$isJump = count($this->level->getCollisionBlocks($bb->offset($x, 1.2, $z))) <= 0;
					if(count($this->level->getCollisionBlocks($bb->offset(0, 0.1, $z))) > 0){
						if($isJump){
							$y = self::$jump;
							$this->motionZ = $z;
						}
						$z = 0;
					}
					if(count($this->level->getCollisionBlocks($bb->offset($x, 0.1, 0))) > 0){
						if($isJump){
							$y = self::$jump;
							$this->motionX = $x;
						}
						$x = 0;
					}
					$ev = new EntityMotionEvent($this, new Vector3($x, $y, $z));
					$this->server->getPluginManager()->callEvent($ev);
					if($ev->isCancelled()) return false;
					$this->x += $x;
					$this->y += $y;
					$this->z += $z;
				}else{
					$attack = mt_rand(0, 4);
					if($attack < 2 && $attack > 2){
						$source = new EntityDamageByEntityEvent($this, $target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $attack);
						$target->attack($attack, $source);
					}
				}
			}
		}
		$bb = clone $this->getBoundingBox();
		$onGround = count($this->level->getCollisionBlocks($bb->offset(0, -$this->gravity, 0))) > 0;
		$this->onGround = $onGround;
		$this->timings->stopTiming();
		parent::onUpdate($currentTick);
		//$hasUpdate = parent::onUpdate($currentTick) || $hasUpdate;
		//return $hasUpdate;
	}
	
	/*public function updateMovement(){
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
	
	public function zupdateMovement(){
		if($this->x !== $this->lastX or $this->y !== $this->lastY or $this->z !== $this->lastZ or $this->yaw !== $this->lastYaw or $this->pitch !== $this->lastPitch){
			$this->lastX = $this->x;
			$this->lastY = $this->y;
			$this->lastZ = $this->z;
			$this->lastYaw = $this->yaw;
			$this->lastPitch = $this->pitch;
			$pk = new MovePlayerPacket();
			$pk->eid = $this->id;
			$pk->x = $this->x;
			$pk->y = $this->y;
			$pk->z = $this->z;
			$pk->yaw = $this->yaw;
			$pk->pitch = $this->pitch;
			$pk->bodyYaw = $this->yaw;
			foreach($this->hasSpawned as $player){
				$player->dataPacket($pk);
			}
		}
		if(($this->lastMotionX != $this->motionX or $this->lastMotionY != $this->motionY or $this->lastMotionZ != $this->motionZ)){
			$this->lastMotionX = $this->motionX;
			$this->lastMotionY = $this->motionY;
			$this->lastMotionZ = $this->motionZ;
			foreach($this->hasSpawned as $player){
				$player->addEntityMotion($this->id, $this->motionX, $this->motionY, $this->motionZ);
			}
		}
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
	
	public function getBoundingBox(){
		$this->boundingBox = new AxisAlignedBB(
			$x = $this->x - $this->width / 2,
			$y = $this->y - $this->height / 2 + $this->stepHeight,
			$z = $this->z - $this->length / 2,
			$x + $this->width,
			$y + $this->height - $this->stepHeight,
			$z + $this->length
		);
		return $this->boundingBox;
	}
	
	/*public function knockBack(Entity $attacker, $damage, $x, $z, $base = 0.4){
		parent::knockBack($attacker, $damage, $x, $z, $base);
		$this->knockback = time() + 1;
	}*/
	
	/*public function kill(){
		parent::kill();
		$this->server->getDarkBotAI->spaw($this->getNameTag(), $this->level);
	}*/
	
	public function attack($damage, EntityDamageEvent $source){
		/*if(!$source->isCancelled() && $source instanceof EntityDamageByEntityEvent){
			$dmg = $source->getDamager();
			if($dmg instanceof Player){
				$this->target = $dmg;
				parent::attack($damage, $source);
				$this->knockbackTicks = 10;
	 }
  }*/
}
}