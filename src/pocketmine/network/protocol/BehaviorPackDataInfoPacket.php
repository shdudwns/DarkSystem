<?php

namespace pocketmine\network\protocol;

class BehaviorPackDataInfoPacket extends PEPacket{

	const NETWORK_ID = Info::BEHAVIOR_PACK_DATA_INFO_PACKET;
	const PACKET_NAME = "BEHAVIOR_PACK_DATA_INFO_PACKET";
	
	public function decode($playerProtocol) {
		
	}
	
	public function encode($playerProtocol) {
		$this->reset($playerProtocol);
		$this->putString('53644fac-a276-42e5-843f-a3c6f169a9ab');
		$this->putInt(1);
		$this->putInt(0);
		$this->putLong(1);
		$this->putString('resources');
	}
	
}
