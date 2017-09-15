<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\network\protocol\types;

interface InventoryNetworkIds{

	const INVENTORY = -1;
	const CONTAINER = 0;
	const WORKBENCH = 1;
	const FURNACE = 2;
	const ENCHANTMENT = 3;
	const BREWING_STAND = 4;
	const ANVIL = 5;
	const DISPENSER = 6;
	const DROPPER = 7;
	const HOPPER = 8;
	const CAULDRON = 9;
	const MINECART_CHEST = 10;
	const MINECART_HOPPER = 11;
	const HORSE = 12;
}