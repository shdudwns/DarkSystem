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

class CommandBlockInventory extends ContainerInventory{
	
	public function __construct(CommandBlock $tile){
		parent::__construct($tile, InventoryType::get(InventoryType::COMMAND_BLOCK));
	}
	
	public function getHolder(){
		return $this->holder;
	}
}