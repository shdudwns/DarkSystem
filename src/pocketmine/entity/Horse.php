<?php

namespace pocketmine\entity;

use pocketmine\Player;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\MobArmorEquipmentPacket;

class Horse extends Living
{
    const NETWORK_ID = 23;

    public function getName(): string
    {
        return "Horse";
    }

    public function setChestPlate($id)
    {
        $pk = new MobArmorEquipmentPacket();
        $pk->eid = $this->getId();
        $pk->slots = [
            ItemItem::get(0, 0),
            ItemItem::get($id, 0),
            ItemItem::get(0, 0),
            ItemItem::get(0, 0)
        ];
        foreach ($this->level->getPlayers() as $player) {
            $player->dataPacket($pk);
        }
    }

    public function spawnTo(Player $player)
    {
        $pk = new AddEntityPacket();
        $pk->eid = $this->getId();
        $pk->type = self::NETWORK_ID;
        $pk->x = $this->x;
        $pk->y = $this->y;
        $pk->z = $this->z;
        $pk->speedX = $this->motionX;
        $pk->speedY = $this->motionY;
        $pk->speedZ = $this->motionZ;
        $pk->yaw = $this->yaw;
        $pk->pitch = $this->pitch;
        $pk->metadata = $this->dataProperties;
        $player->dataPacket($pk);

        parent::spawnTo($player);
    }

}
