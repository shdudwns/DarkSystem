<?php

namespace pocketmine\network\protocol;

class BehaviorPackClientResponsePacket extends PEPacket{

	const NETWORK_ID = Info::BEHAVIOR_PACK_CLIENT_RESPONSE_PACKET;
	const PACKET_NAME = "BEHAVIOR_PACK_CLIENT_RESPONSE_PACKET";
	
	const STATUS_REFUSED = 1;
	const STATUS_SEND_PACKS = 2;
	const STATUS_HAVE_ALL_PACKS = 3;
	const STATUS_COMPLETED = 4;

	public $status;
	public $packIds = [];

	public function decode($playerProtocol) {
		$this->status = $this->getByte();
		$entryCount = $this->getLShort();
		while ($entryCount-- > 0) {
			$this->packIds[] = $this->getString();
		}
	}

	public function encode($playerProtocol) {
		
	}

}
