<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\darkbot;

use pocketmine\Thread;
use pocketmine\Server;
use pocketmine\Player;

class ChatHandler extends Thread{
	
	private $server;
	
	public function __construct(Server $server){
		$player->server = $server;
	}
	
	public function getThreadName(){
		return "ChatHandler";
	}
	
	public function check($player, $message){
		$dbotprefix = $player->getServer()->getDarkBotPrefix();
		$msg = "§aSizin İsteğiniz Benim İçin Bir Emirdir!";
		switch($message){
			case "dbot gm1":
			case "gm1 dbot":
			case "darkbot gm1":
			case "gm1 darkbot":
			$player->setGamemode(1);
			$player->sendMessage($dbotprefix . $msg);
			return true;
			break;
			case "dbot gm0":
			case "gm0 dbot":
			case "darkbot gm0":
			case "gm0 darkbot":
			$player->setGamemode(0);
			$player->sendMessage($dbotprefix . $msg);
			return true;
			break;
			case "dbot spawn":
			case "spawn dbot":
			case "darkbot spawn":
			case "spawn darkbot":
			$player->teleport($player->getServer()->getDefaultLevel()->getSafeSpawn());
			$player->sendMessage($dbotprefix . $msg);
			return true;
			break;
			case "dbot kill":
			case "kill dbot":
			case "darkbot kill":
			case "kill darkbot":
			$player->kill();
			$player->sendMessage($dbotprefix . $msg);
			return true;
			break;
			case "#darkbot naber":
			$player->server->broadcastMessage($dbotprefix . "§aİyiyim, Bana Böyle Şeyler Söylemen Hoşuma Gidiyor :)");
			break;
			case "#darkbot sen delisin":
			$player->server->broadcastMessage($dbotprefix . "§aSus la, deli sensin!");
			break;
			case "#darkbot dark nerde":
			$player->server->broadcastMessage($dbotprefix . "§aBen Ne Bileyim?");
			break;
			case "#darkbot iyimisin":
			case "#darkbot iyi misin":
			$player->server->broadcastMessage($dbotprefix . "§aEvet, Peki Ya sen?");
			break;
			case "#bende":
			case "#bende iyiyim":
			$player->server->broadcastMessage($dbotprefix . "§aBuna Sevindim.");
			break;
			default;
			break;
		}
	}
}