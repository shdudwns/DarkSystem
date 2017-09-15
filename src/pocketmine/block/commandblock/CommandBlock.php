<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\block\commandblock;

use pocketmine\item\Item;
use pocketmine\Server;

class CommandBlock extends Solid
{
    protected $id = self::COMMAND_BLOCK;

    public function __construct($meta = 0)
    {
        $this->meta = $meta;
    }

    public function canBeActivated()
    {
        return true;
    }

    public function getName()
    {
        return "Command Block";
    }

    public function getHardness()
    {
        return -1;
    }
    
    public function activate()
    {
    	$command = $this->getServer()->getSoftConfig(""); //TODO
        $this->getServer()->dispatchCommand($command, 1);
    }

}
