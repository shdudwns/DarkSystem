<?php

namespace pocketmine\network\protocol;

class BehaviorPackStackPacket extends PEPacket{

	const NETWORK_ID = Info::BEHAVIOR_PACK_STACK_PACKET;
	const PACKET_NAME = "BEHAVIOR_PACK_STACK_PACKET";

	public function decode($playerProtocol) {
		
	}
	
	public function encode($playerProtocol) {
		$this->reset($playerProtocol);
		$this->putByte(0);
		$this->putVarInt(0);
		$this->putVarInt(0);
	}

}
