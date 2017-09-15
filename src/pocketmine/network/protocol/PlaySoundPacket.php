<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\network\protocol;

class PlaySoundPacket extends PEPacket{

	const NETWORK_ID = Info::PLAY_SOUND_PACKET;
	const PACKET_NAME = "PLAY_SOUND_PACKET";
	
	public $sound;
	public $x;
	public $y;
	public $z;
	public $volume;
	public $float;

	public function decode($playerProtocol){
		$this->sound = $this->getString();
		$this->getBlockPos($this->x, $this->y, $this->z);
		$this->volume = $this->getFloat();
		$this->float = $this->getFloat();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putString($this->sound);
		$this->putBlockPos($this->x, $this->y, $this->z);
		$this->putFloat($this->volume);
		$this->putFloat($this->float);
	}
	
}
