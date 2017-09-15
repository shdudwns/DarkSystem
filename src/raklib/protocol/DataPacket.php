<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace raklib\protocol;

abstract class DataPacket extends Packet{
	
    public $packets = [];

    public $seqNumber;

    public function encode(){
        parent::encode();
        $this->putLTriad($this->seqNumber);
        foreach($this->packets as $packet){
            $this->put($packet instanceof EncapsulatedPacket ? $packet->toBinary() : (string) $packet);
        }
    }

    public function length(){
        $length = 4;
        foreach($this->packets as $packet){
            $length += $packet instanceof EncapsulatedPacket ? $packet->getTotalLength() : strlen($packet);
        }

        return $length;
    }

    public function decode(){
        parent::decode();
        $this->seqNumber = $this->getLTriad();

        while(!$this->feof()){
            $offset = 0;
			$data = substr($this->buffer, $this->offset);
            $packet = EncapsulatedPacket::fromBinary($data, false, $offset);
            $this->offset += $offset;
            if(strlen($packet->buffer) === 0){
                break;
            }
            $this->packets[] = $packet;
        }
    }

	public function clean(){
		$this->packets = [];
		$this->seqNumber = null;
		return parent::clean();
	}
}
