<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine;

use pocketmine\permission\ServerOperator;

interface IPlayer extends ServerOperator{
	
	public function isOnline();
	
	public function getName();
	
	public function isBanned();
	
	public function setBanned($banned);
	
	public function isWhitelisted();
	
	public function setWhitelisted($value);
	
	public function getPlayer();
	
	public function getFirstPlayed();
	
	public function getLastPlayed();
	
	public function hasPlayedBefore();
}
