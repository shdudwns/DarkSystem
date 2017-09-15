<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\utils;

class MySQLManager extends DataBase{

	private $server;
	private $dbname;
	private $database;

	public function __construct($server,$host,$user,$pass,$dbname,$port = 19132){
		$this->server = $server;
		$this->dbname = $dbname;
		$this->database = new \mysqli($host,$user,$pass,$dbname,$port);
	}

	public function Connect(){
		if($this->database->connect_error){
			return false;
		}else{

			$sql = "CREATE TABLE IF NOT EXISTS InventoryData (name VARCHAR(20) NOT NULL,slot VARCHAR(3) NOT NULL,id VARCHAR(3) NOT NULL,meta VARCHAR(2) NOT NULL,count VARCHAR(2) NOT NULL,PRIMARY KEY (name,slot))";
			$this->db->query($sql);
			$sql = "CREATE TABLE IF NOT EXISTS PlayerData (name VARCHAR(20) NOT NULL,gametype INT(1) NOT NULL,lastplayed INT(10) NOT NULL,hunger Int(2) NOT NULL,health Int(3) NOT NULL,maxhealth Int(3) NOT NULL,exp Int(1000000) NOT NULL,explevel Int(1000) NOT NULL,PRIMARY KEY (name))";
			$this->db->query($sql);

			return true;
		}
	}

	public function Ping(){
		$this->database->ping();
	}

	public function loadInventory($player){

		$sql = "SELECT `name`,`slot`,`id`,`meta`,`count` FROM InventoryData WHERE name='".strtolower($player->getName())."'";
		$res = $this->db->query($sql);
		if(!$res === false){
			while(($row = $res->fetch_assoc()) != null){
				$player->getInventory()->setItem($row["slot"],Item::get($row["id"],$row["meta"],$row["count"]));
			}
		}
	}

	public function saveInventory($player){
		$name = strtolower($player->getName());

		$sql = "DELETE FROM `".$this->dbname."`.`InventoryData` WHERE `InventoryData`.`name` = '".$name."'";
		$this->db->query($sql);

		$inventory = $player->getInventory();

		foreach ($inventory->getContents() as $slot=>&$item){
			$id = $item->getId();
			$meta = $item->getDamage();
			$count = $item->getCount();
			$sql = "INSERT INTO `".$this->dbname."`.`InventoryData` (`name`,`slot`,`id`,`meta`,`count`)VALUES ('".$name."','".$slot."','".$id."','".$meta."','".$count."')";
			$this->db->query($sql);
		}
	}

	public function registerPlayer($name){
			$sql = "INSERT INTO `".$this->dbname."`.`PlayerData` (`name`,`gamemode`,`lastplayed`,`hunger`,`health`,`maxhealth`,`exp`,`explevel`)VALUES ('".$name."','".$slot."','".$id."','".$meta."','".$count."')";
			$this->db->query($sql);
	}

	public function savePlayer($player){

		$GameType = $player->gamemode;
		$lastPlayed = floor(microtime(true) * 1000);
		$Hunger = $player->food;
		$Health = $player->getHealth();
		$MaxHealth = $player->getMaxHealth();
		$Experience = $player->exp;
		$ExpLevel = $player->expLevel;



	}

}