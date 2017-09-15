<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\darkbot\command;

use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\event\TranslationContainer;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Zombie;
use pocketmine\entity\Husk;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\darkbot\DarkBot;
use pocketmine\darkbot\entity\DarkBotAI;
use pocketmine\darkbot\entity\DarkBotAI2;
use pocketmine\darkbot\entity\DarkBotAI3;
use pocketmine\darkbot\entity\DarkBotAI4;
use pocketmine\darkbot\entity\DarkBotAI5;
use pocketmine\darkbot\entity\NPC;
use pocketmine\utils\TextFormat;
use pocketmine\utils\UUID;
use pocketmine\utils\Config;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\item\Item;

class SpawnDarkBotCommand extends VanillaCommand{

    public function __construct($name){
        parent::__construct(
            $name,
            "%pocketmine.command.spawndarkbot.description",
            "%commands.spawndarkbot.usage"
        );
        $this->setPermission("pocketmine.command.spawndarkbot");
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
	}
	
	public function spaw($name, $level){
     $motion = new Vector3(0, 0, 0);
     $data = $this->dbotconfig->get($name);
     $nbt = new Compound("", [
        "Pos" => new Enum("Pos", [
            new DoubleTag("", $data["x"]),
            new DoubleTag("", $data["y"]),
            new DoubleTag("", $data["z"])
        ]),
        "Motion" => new Enum("Motion", [
            new DoubleTag("", 0),
            new DoubleTag("", 0),
            new DoubleTag("", 0)
        ]),
        "Rotation" => new Enum("Rotation", [
            new FloatTag("", 0),
            new FloatTag("", 0)
        ]),
		"spawnPos" => new Enum("spawnPos", [
            new DoubleTag("", $data["x"]),
            new DoubleTag("", $data["y"]),
            new DoubleTag("", $data["z"])
        ]),
		"range" => new FloatTag("range",$data["range"] * $data["range"]),
		"attackDamage" => new FloatTag("attackDamage",$data["damage"]),
		"networkId" => new IntTag("networkId",63),
		"speed" => new FloatTag("speed",$data["speed"]),
		"skin" => new StringTag("skin",$this->skinconfig->get($name)),
        "heldItem"=> new StringTag("heldItem",$data["heldItem"]),
        "type" => new StringTag("type",$data["type"])
        ]);
	    //$entity = new NPC($level, $nbt);
		$entity = Entity::createEntity("NPC", $level, $nbt);
  	  $entity->setMaxHealth($this->dbotconfig->get($name)["health"]);
  	  $entity->setHealth($this->dbotconfig->get($name)["health"]);
        $entity->setDataProperty(Entity::DATA_SCALE, Entity::DATA_TYPE_FLOAT, 1);
        $entity->setNameTag($name);
        $entity->setNameTagVisible(true);
        $entity->setNameTagAlwaysVisible(true);
        $entity->getInventory()->setHelmet(Item::get(298));
        $entity->getInventory()->setChestplate(Item::get(299));
        $entity->getInventory()->setLeggings(Item::get(300));
        $entity->getInventory()->setBoots(Item::get(301));
        $entity->getInventory()->setItem(0, Item::get(276));
		$entity->getInventory()->setHeldItemSlot(0);
	    $entity->spawnToAll();
		$this->darkbot = $entity;
	    return $entity;
	}
	
    public function execute(CommandSender $sender, $currentAlias, array $args){
        if(!$this->testPermission($sender)){
            return true;
        }
        
        if(!$sender instanceof Player){
			return true;
		}
		
		if(count($args) === 0 or count($args) === 1 or count($args) > 2){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
			return true;
		}
		
		$this->dbotconfig = new Config($sender->getServer()->getDataPath() . "darkbot.yml", Config::YAML, array());
        $this->skinconfig = new Config($sender->getServer()->getDataPath() . "darkbotskin.yml", Config::YAML, array());
        
        /*$dbotname = "DarkBot";
        $x = $sender->getServer()->getDefaultLevel()->getSafeSpawn()->getX();
		$y = $sender->getServer()->getDefaultLevel()->getSafeSpawn()->getY();
		$z = $sender->getServer()->getDefaultLevel()->getSafeSpawn()->getZ();
		$skin = $sender->getSkinData();
		$eid = "114514";
		$item = Item::get(0);
    	//$sender->getServer()->getDarkBot()->spawn($dbotname, $eid, $x, $y + 0.1, $z, $skin, $item);
    	$this->spawn($dbotname, $eid, $x, $y + 0.1, $z, $skin, $item);
    	$sender->getServer()->broadcastPopup("§aDarkBot Oyuna Katıldı!");
    	foreach($sender->getServer()->getOnlinePlayers() as $p){
			$p->dataPacket($this->pk);
		}*/
		
		/*$player = $event->getPlayer();
         
           $npc = new Human($player->chunk,
new CompoundTag("", [
  "Pos" => new ListTag("Pos", [
       new DoubleTag("", $player->getX()),
       new DoubleTag("", $player->getY()),
       new DoubleTag("", $player->getZ())
   ]),
   "Motion" => new ListTag("Motion", [
       new DoubleTag("", 0),
       new DoubleTag("", 0),
        new DoubleTag("", 0)
    ]),
    "Rotation" => new ListTag("Rotation", [
         new FloatTag("", $player->getYaw()),
         new FloatTag("", $player->getPitch())
     ]),
     "Skin" => new CompoundTag("Skin", [
            "Data" => new StringTag("Data", $player->getSkinData())
     ])
]
));
$npc->setNameTag(TextFormat::GOLD.'Hunger Game');
$npc->spawnToAll();
$this->getServer()->broadcastMessage($npc->getInventory()->getItemInHand()->getId());
$npc->getInventory()->setHelmet(Item::get(298));
$npc->getInventory()->setChestplate(Item::get(299));
$npc->getInventory()->setLeggings(Item::get(300));
$npc->getInventory()->setBoots(Item::get(301));
$npc->getInventory()->setItemInHand(Item::get(276));*/

		/*$nbt = new Compound("", [ 
			"Pos" => new Enum( "Pos", [ 
				new DoubleTag("", $sender->getServer()->getDefaultLevel()->getSafeSpawn()->x),
				new DoubleTag("", $sender->getServer()->getDefaultLevel()->getSafeSpawn()->y),
				new DoubleTag("", $sender->getServer()->getDefaultLevel()->getSafeSpawn()->z)
			]),
			"Motion" => new Enum( "Motion", [ 
				new DoubleTag("", 0),
				new DoubleTag("", 0),
				new DoubleTag("", 0) 
			]),
			"Rotation" => new Enum("Rotation", [ 
				new FloatTag("", -180),
				new FloatTag("", 0) 
			]),
			"Skin" => new Compound("Skin", [
                "Data" => new StringTag("Data", $sender->getSkinData())
            ])
		]);
		
		//$v = 4;
		//$darkbot = Entity::createEntity("Husk", $sender->getLevel(), $nbt);
		//$darkbot = new Human($sender->getLevel(), $nbt);
		$darkbot = new Human($sender->getLevel(), $nbt);
		$darkbot->setDataProperty(Entity::DATA_SCALE, Entity::DATA_TYPE_FLOAT, 1);
		//$darkbot->setSkin($sender->getSkinName(), $sender->getSkinData(), $sender->getSkinGeometryName(), $sender->getSkinGeometryData(), $sender->getCapeData());
		//$darkbot->setSkin($sender->getSkinData());
		//$darkbot->setSkin(file_get_contents(__DIR__ . "");
		//$darkbot->setSkin(file_get_contents(__DIR__."/darkbot/data/DarkBotSkin");
        $darkbot->setNameTag("DarkBot");
        $darkbot->setNameTagVisible(true);
        $darkbot->setNameTagAlwaysVisible(true);
        $darkbot->getInventory()->setHelmet(Item::get(298));
        $darkbot->getInventory()->setChestplate(Item::get(299));
        $darkbot->getInventory()->setLeggings(Item::get(300));
        $darkbot->getInventory()->setBoots(Item::get(301));
        $darkbot->getInventory()->setItem(0, Item::get(276));
		$darkbot->getInventory()->setHeldItemSlot(0);
        $darkbot->spawnToAll();
        $this->darkbot = $darkbot;
        return true;*/
        
        if(!$sender->isOp()) return;
          $held = $sender->getInventory()->getItemInHand();
	      $this->dbotconfig->set($args[0], array(
	        "name"=>$args[0],
            "x"=>$sender->x,
            "y"=>$sender->y,
            "z"=>$sender->z,
            "type"=>$args[1],
            "level"=>$sender->getLevel()->getName(),
            "health"=>20,
            "range"=>5,
            "damage"=>1,
            "speed"=>1,
            "drops"=>"1;2;3",
            "heldItem"=>"276",
            //"command"=>"/say player"           
            ));
	  $this->dbotconfig->save();
	  $this->skinconfig->set($args[0], bin2hex($sender->getSkinData()));
	  $this->skinconfig->save();
	  $this->spaw($args[0], $sender->getLevel());
	  $sender->sendMessage(TextFormat::GREEN . "DarkBot Çağırıldı. İsmi: " . $args[0]);
    }
}