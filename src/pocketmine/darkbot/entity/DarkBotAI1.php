<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\darkbot\entity;

use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\level\Level;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\Player;

class DarkBotAI1 extends Human{
	
	public function getName(): string{
        return "DarkBot";
    }
    
    public function spawnTo(Player $player){
        $uuid = $this->getUniqueId();
        $entityId = $this->getId();

        $pk = new AddPlayerPacket();
        $pk->uuid = $uuid;
        $pk->username = "DarkBot";
        $pk->eid = $entityId;
        $pk->x = $this->x;
        $pk->y = $this->y;
        $pk->z = $this->z;
        $pk->yaw = $this->yaw;
        $pk->pitch = $this->pitch;
        //$pk->item = $this->getInventory()->getItemInHand();
        $pk->metadata = $this->dataProperties;
        $pk->metadata[self::DATA_NAMETAG] = [self::DATA_TYPE_STRING, "DarkBot"];
        $player->dataPacket($pk);
        //$this->inventory->sendArmorContents($player);
        $player->getServer()->updatePlayerListData($uuid, $entityId, "DarkBot", $this->skinName, $this->skin, $this->skinGeometryName, $this->skinGeometryData, $this->capeData, [$player]);
        
        parent::spawnTo($player);
    }
    
}
