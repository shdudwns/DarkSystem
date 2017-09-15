<?php

namespace pocketmine\network\protocol;

class BehaviorPackInfoPacket extends PEPacket{

	const NETWORK_ID = Info::BEHAVIOR_PACK_INFO_PACKET;
	const PACKET_NAME = "BEHAVIOR_PACK_INFO_PACKET";
	
	public function decode($playerProtocol) {
		
	}
	
	public function encode($playerProtocol) {
		$this->reset($playerProtocol);
		$this->putByte(0);
		$this->putShort(0);
		$this->putShort(0);
		
	}

}
