<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\command\defaults;

use pocketmine\event\Listener;
use pocketmine\event\TranslationContainer;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TpAllCommand extends VanillaCommand{
	
	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.tpall.description",
			"%commands.tpall.usage"
		);
		$this->setPermission("pocketmine.command.tpall");
	}
	
	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "Bu Komutu Sadece Oyuncular Kullanabilir!");
			return true;
		}
		
		if(count($args) >= 1){
			$sender->sendMessage(TextFormat::RED . "Yanlış Kullanış!");
			return true;
		}
		
		$players = count($sender->getServer()->getOnlinePlayers());
        if($players <= 1){
        	$sender->sendMessage(TextFormat::RED . "Hiçbir Oyuncu Aktif Değil!");
        	return true;
        }else{
        	foreach($sender->getServer()->getOnlinePlayers() as $p){
        	    $p->teleport($sender);
			}
		}
		
		$sender->sendMessage(TextFormat::GREEN . "Başarılı!");
		return true;
	}
}