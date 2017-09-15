<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\darkbot;

use pocketmine\Thread;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\level\Level;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\utils\MainLogger;
use pocketmine\utils\Utils;
use pocketmine\utils\UUID;

class DarkBot extends Thread{
	
	private $server;
	
	const PREFIX = "§bDARKBOT: §r";
	
	public function __construct(Server $server){
		$this->server = $server;
	}
	
	public function getThreadName(){
		return "DarkBot";
	}
	
	public function getDarkBotVersion(){
		return \pocketmine\DARKBOT_VERSION;
	}
	
	public function getStartupMessage(){
		return self::PREFIX . "§aSunucuyu Ben Yönetiyorum!";
	}
	
	public function check(){
		if($active = true){
			return "§aAktif";
		}else{
			return "§cDevre Dışı";
		}
	}
	
	public function run(){
		//
		$enabled = true;
		//
		if($enabled = true){
			$active = true;
		}else{
			$active = false;
		}
	}
	
	public function shutdown(){
		$active = false;
	}
	
	public function spawn($name, $eid, $x, $y, $z, $skin, $item){
		$this->pk = new AddPlayerPacket();
		$this->pk->uuid = UUID::fromRandom();
		$this->pk->username = $name;
		$this->pk->eid = $eid;
		$this->pk->x = $x;
		$this->pk->y = $y;
		$this->pk->z = $z;
		$this->pk->skin = $skin;			
		$this->pk->speedX = 0;
		$this->pk->speedY = 0;
		$this->pk->speedZ = 0;
		$this->pk->yaw = 0;
		$this->pk->pitch = 0;
		$this->pk->item = $item;
		$this->pk->metadata = [
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_BYTE, 0],
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_BYTE, 0 << Entity::DATA_FLAG_SILENT],
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_BYTE, 1 << Entity::DATA_FLAG_NO_AI],
			//Entity::DATA_FLAGS => [Entity::DATA_FLAG_SHOW_NAMETAG, true],
			//Entity::DATA_FLAGS => [Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG, true],
			Entity::DATA_LEAD_HOLDER => [Entity::DATA_TYPE_LONG, -1],
			Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 1],
		];
		
		foreach($this->server->getOnlinePlayers() as $p){
			$p->dataPacket($this->pk);
			//$p->sendMessage("§bDARKBOT: §amerhaba!");
		}
		
		//$this->server->broadcastPopup("§aDarkBot Oyuna Katıldı!");
	}
	
	public function attackDDoS($player){
		//$player->setPing($player, 300);
	}
	
	public function activateShield(){
		//$this->server->disableOPCommand();
	}
	
	/*public function fixPlugin($plugin){
		$badcode1 = "")"
		$goodcode1 = "");"
		if(strpos($plugin, $badcode1){
			$plugin->set($badcode1, $goodcode1);
		}else{
			return;
		}
	}*/
	
	/*public function fixDarkSystem($file){
		$badcode1 = "")"
		$goodcode1 = "");"
		if(strpos($file, $badcode1){
			$file->set($badcode1, $goodcode1);
		}else{
			return;
		}
	}*/

	public function banHacker($player){
		$word = "hacker";
		if(strpos($player->getName(), $word)){
			//Ban
			return;
		}
	}

	public function warn($player){
		$player->sendMessage("§cUyarıldınız!");
	}

	public function giveGift(){
		
	}

	public function crash(){
		$this->startDS();
		MainLogger::getLogger()->info("Bir Çökme Kurtarıldı.");
	}

	public function eatDDoS(){
		$this->blockDDoS();
	}
	
	public function startDS(){
		$this->server->run();
	}
	
	public function blockDDoS(){
		
	}
	
}