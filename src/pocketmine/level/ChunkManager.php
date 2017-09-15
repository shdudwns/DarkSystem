<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\level;

interface ChunkManager{
	
	public function getBlockIdAt($x, $y, $z);
	
	public function setBlockIdAt($x, $y, $z, $id);
	
	public function getBlockDataAt($x, $y, $z);
	
	public function setBlockDataAt($x, $y, $z, $data);
	
	public function getYMask();
	
	public function getMaxY();
}