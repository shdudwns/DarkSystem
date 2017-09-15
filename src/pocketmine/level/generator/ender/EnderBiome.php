<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\level\generator\ender;

use pocketmine\level\generator\biome\Biome;

class EnderBiome extends Biome
{

    public function getName(): string
    {
        return "Ender";
    }

    public function getColor()
    {
        return 0;
    }

    public function __construct()
    {

    }
}