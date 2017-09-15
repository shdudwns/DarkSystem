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

class AvailableCommandsPacket extends PEPacket{

	const NETWORK_ID = Info::AVAILABLE_COMMANDS_PACKET;
	const PACKET_NAME = "AVAILABLE_COMMANDS_PACKET";
	
	static private $commandsBuffer = [];
	
	public $commands;
	//public $unknown;
	
	public function decode($playerProtocol){
	}
	
	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putString($this->commands);
		//$this->putString($this->unknown);
	}
	
	public static function prepareCommands($commands){
		self::$commandsBuffer['default'] = json_encode($commands);
		$enumValues = [];
		$enumValuesCount = 0;
		$enumAdditional = [];
		$enums = [];
		$commandsStream = new BinaryStream();
		foreach($commands as $commandName => $commandData){
			/*if($commandName == 'help'){
				continue;
			}*/
			$commandsStream->putString($commandName);
			$commandsStream->putString($commandData['versions'][0]['description']);
			$commandsStream->putByte(0);
			$commandsStream->putByte(0);
			if(isset($commandData['versions'][0]['aliases']) && !empty($commandData['versions'][0]['aliases'])){
				$aliases = [];
				foreach($commandData['versions'][0]['aliases'] as $alias){
					if(!isset($enumAdditional[$alias])){
						$enumValues[$enumValuesCount] = $alias;
						$enumAdditional[$alias] = $enumValuesCount;
						$targetIndex = $enumValuesCount;
						$enumValuesCount++;
					}else{
						$targetIndex = $enumAdditional[$alias];
					}
					$aliases[] = $targetIndex;
				}
				$enums[] = [
					'name' => $commandName . 'CommandAliases',
					'data' => $aliases,
				];
				$aliasesEnumId = count($enums) - 1;
			}else{
				$aliasesEnumId = -1;
			}
			$commandsStream->putLInt($aliasesEnumId);
			$commandsStream->putVarInt(count($commandData['versions'][0]['overloads']));
			foreach($commandData['versions'][0]['overloads'] as $overloadData){
				$commandsStream->putVarInt(count($overloadData['input']['parameters']));
				foreach($overloadData['input']['parameters'] as $paramData){
					$commandsStream->putString($paramData['name']);
					$commandsStream->putLInt(0);
					$commandsStream->putByte(isset($paramData['optional']) && $paramData['optional']);
				}
			}
		}
		$additionalDataStream = new BinaryStream();
		$additionalDataStream->putVarInt($enumValuesCount);
		for($i = 0; $i < $enumValuesCount; $i++){
			$additionalDataStream->putString($enumValues[$i]);
		}
		$additionalDataStream->putVarInt(0);
		$enumsCount = count($enums);
		$additionalDataStream->putVarInt($enumsCount);
		for($i = 0; $i < $enumsCount; $i++){
			$additionalDataStream->putString($enums[$i]['name']);
			$dataCount = count($enums[$i]['data']);
			$additionalDataStream->putVarInt($dataCount);
			for($j = 0; $j < $dataCount; $j++){
				if($enumValuesCount < 256){
					$additionalDataStream->putByte($enums[$i]['data'][$j]);
				}elseif($enumValuesCount < 65536){
					$additionalDataStream->putLShort($enums[$i]['data'][$j]);
				}else{
					$additionalDataStream->putLInt($enums[$i]['data'][$j]);
				}	
			}
		}
		$additionalDataStream->putVarInt(count($commands));
		$additionalDataStream->put($commandsStream->buffer);
		self::$commandsBuffer[Info::PROTOCOL_120] = $additionalDataStream->buffer;
	}
}