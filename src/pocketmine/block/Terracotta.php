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

use pocketmine\item\Tool;

class Terracotta extends Solid
{

    protected $id = self::TERRACOTTA;

    const TERRACOTTA_WHITE = 0;
    const TERRACOTTA_ORANGE = 1;
    const TERRACOTTA_MAGENTA = 2;
    const TERRACOTTA_LIGHT_BLUE = 3;
    const TERRACOTTA_YELLOW = 4;
    const TERRACOTTA_LIME = 5;
    const TERRACOTTA_PINK = 6;
    const TERRACOTTA_GRAY = 7;
    const TERRACOTTA_LIGHT_GRAY = 8;
    const TERRACOTTA_CYAN = 9;
    const TERRACOTTA_PURPLE = 10;
    const TERRACOTTA_BLUE = 11;
    const TERRACOTTA_BROWN = 12;
    const TERRACOTTA_GREEN = 13;
    const TERRACOTTA_RED = 14;
    const TERRACOTTA_BLACK = 15;

    public function __construct($meta = 0)
    {
        $this->meta = $meta;
    }

    public function getHardness()
    {
        return 1.25;
    }

    public function getToolType()
    {
        return Tool::TYPE_PICKAXE;
    }

    public function getName(): string
    {
        static $names = [
            0 => "White Terracotta",
            1 => "Orange Terracotta",
            2 => "Magenta Terracotta",
            3 => "Light Blue Terracotta",
            4 => "Yellow Terracotta",
            5 => "Lime Terracotta",
            6 => "Pink Terracotta",
            7 => "Gray Terracotta",
            8 => "Light Gray Terracotta",
            9 => "Cyan Terracotta",
            10 => "Purple Terracotta",
            11 => "Blue Terracotta",
            12 => "Brown Terracotta",
            13 => "Green Terracotta",
            14 => "Red Terracotta",
            15 => "Black Terracotta",
        ];
        return $names[$this->meta & 0x0f];
    }

}