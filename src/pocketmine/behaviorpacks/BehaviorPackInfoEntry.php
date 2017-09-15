<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\behaviorpacks;

class BehaviorPackInfoEntry{
	
	protected $packId;
	protected $version;
	protected $packSize;

	public function __construct($packId, $version, $packSize = 0){
		$this->packId = $packId;
		$this->version = $version;
		$this->packSize = $packSize;
	}

	public function getPackId(){
		return $this->packId;
	}

	public function getVersion(){
		return $this->version;
	}

	public function getPackSize(){
		return $this->packSize;
	}

}