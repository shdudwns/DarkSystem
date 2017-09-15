<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace ZombieAI;

use pocketmine\Plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\scheduler\CallbackTask;
use pocketmine\math\Vector3;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\item\Item;
use pocketmine\entity\Zombie;

class ZombieAI extends PluginBase implements Listener{
	
	public function onEnable(){
		$this->getServer ()->getScheduler ()->scheduleRepeatingTask ( new CallbackTask ( [
			$this,
			"Zombie"
		] ), 1);
		
		$this->getServer ()->getPluginManager ()->registerEvents ( $this, $this );
	}
	
	public function Zombie(){
		
		$MobAI = new MobAI();
		
		foreach ($this->getServer()->getLevels() as $level) {
			foreach ($level->getEntities() as $zo){
				if ($zo instanceof Zombie){
					$MobAI->burn($zo);
					
					$player = $MobAI->searchplayer($zo);
					
					if($player){
						$MobAI->Chase($zo,$player);
					}else{
						$MobAI->freewalk($zo);
					}
				}
			}
		}	
	}
	
	public function Damage(EntityDamageEvent $event){
		if($event instanceof EntityDamageByEntityEvent){
			$p = $event->getDamager();
			$e = $event->getEntity();
			if($e instanceof Zombie and $p instanceof Player){
				$weapon = $p->getInventory()->getItemInHand()->getID();
				$high = 0;
				if ($weapon == 258 or $weapon == 271 or $weapon == 275) {
					$back = 0.7;
				}
				elseif ($weapon == 267 or $weapon == 272 or $weapon == 279 or $weapon == 283 or $weapon == 286) {
					$back = 0.7;
				}
				elseif ($weapon == 276) {
					$back = 0.7;
				}
				elseif ($weapon == 292) {
					$back = 0.7;
					$high = 0.7;
				}
				else {
					$back = 0.5;
				}
				//$e->knockBack($p, 0, - $e->motionX * $back, - $e->motionZ * $back, 0.4);
					//var_dump("玩家".$p->getName()."攻击了ID为".$zo->getId()."的僵尸");
					$backx = $e->x - $back;
					$backy = $e->y + $high;
					$backz = $e->z - $back;
					$pos = new Vector3($backx,$backy,$backz);
					$e->setPosition($pos);			
			}
		}
	}
	
	public function ZombieDeath(EntityDeathEvent $event){//死んだ時に、何をドロップするか。
		$entity = $event->getEntity();
		if ($entity instanceof Zombie) {
			$eid = $entity->getID();
			$ok = mt_rand(0,100);
			if ($ok < 30) {
				$drop = array(new Item(352));
			}
			elseif ($ok >= 30 and $ok < 50) {  //掉羽毛
				$drop = array(new Item(288));
			}
			elseif ($ok >= 50 and $ok < 60) {  //掉胡萝卜
				$drop = array(new Item(391));
			}
			elseif ($ok >= 60 and $ok < 70) {  //掉土豆
				$drop = array(new Item(392));
			}
			elseif ($ok >= 70 and $ok < 75) {  //掉蜘蛛丝
				$drop = array(new Item(287));
			}
			elseif ($ok >= 75 and $ok < 80) {  //掉石英
				$drop = array(new Item(406));
			}
			elseif ($ok >= 80 and $ok < 85) {  //掉铁锭
				$drop = array(new Item(265));
			}
			elseif ($ok >= 85 and $ok < 90) {  //掉金锭
				$drop = array(new Item(266));
			}
			elseif ($ok >= 90 and $ok < 95) {  //掉甘蔗
				$drop = array(new Item(338));
			}
			elseif ($ok >= 95 and $ok < 100) {  //掉萤石粉
				$drop = array(new Item(348));
			}
			elseif ($ok == 100) {  //掉钻石
				$drop = array(new Item(264));
			}
			else {
				$drop = array();
			}
			$event->setDrops($drop);
		}
	}
	
	public function onDisable(){
		
		$util = new AIUtils();
		
		$util->killMobs($this->getServer()->getLevels());
	}
}
	
	
	