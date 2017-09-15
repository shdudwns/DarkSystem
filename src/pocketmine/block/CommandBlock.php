<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\block;

use pocketmine\item\Item;

class CommandBlock extends Solid
{
    protected $id = self::COMMAND_BLOCK;

    public function __construct($meta = 0)
    {
        $this->meta = $meta;
    }

    public function canBeActivated(): bool
    {
        return true;
    }

    public function getName(): string
    {
        return "Command Block";
    }

    public function getHardness()
    {
        return -1;
    }

}
