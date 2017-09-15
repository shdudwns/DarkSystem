<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\level\light;

class SkyLightUpdate extends LightUpdate{

	public function getLight(int $x, int $y, int $z) : int{
		return $this->level->getBlockSkyLightAt($x, $y, $z);
	}

	public function setLight(int $x, int $y, int $z, int $level){
		$this->level->setBlockSkyLightAt($x, $y, $z, $level);
	}
}