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

class Range{
	public $minValue;
	public $maxValue;

	public function __construct(int $min, int $max){
		$this->minValue = $min;
		$this->maxValue = $max;
	}

	public function isInRange(int $v) : bool{
		return $v >= $this->minValue && $v <= $this->maxValue;
	}
}