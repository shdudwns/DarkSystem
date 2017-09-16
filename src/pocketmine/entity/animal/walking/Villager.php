<?php

namespace pocketmine\entity\animal\walking;

use pocketmine\entity\animal\WalkingAnimal;

class Villager extends WalkingAnimal{
	
    const NETWORK_ID = 15;

    public $width = 0.938;
    public $length = 0.609;
    public $height = 2;
    
    public function getName(): string{
        return "Villager";
    }
    
    public function initEntity(){
		parent::initEntity();

		$this->setMaxHealth(10);
	}
	
    public function getSpeed(){
        return 1.1;
    }
    
    public function getDrops(){
        return [];
    }
    
    public function getKillExperience(){
        return mt_rand(3, 6);
    }

}
