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

use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class SetArmorCommand extends VanillaCommand{

    public function __construct($name){
        parent::__construct(
            $name,
            "%pocketmine.command.setarmor.description",
            "%commands.setarmor.usage"
        );
        $this->setPermission("pocketmine.command.setarmor");
    }

    public function execute(CommandSender $sender, $currentAlias, array $args){
        if(!$this->testPermission($sender)){
            return true;
        }
        
        if(!isset($args[0])){
        	$players = count($sender->getServer()->getOnlinePlayers());
        	if($players <= 0){
        	    $sender->sendMessage(TextFormat::RED . "Hiçbir Oyuncu Aktif Değil!");
        	    return true;
        	}
        	
        	foreach($sender->getServer()->getOnlinePlayers() as $p){
        	    $p->givePizza($mark);
        	    $sender->sendMessage(TextFormat::GREEN . "Herkese Pizza Verildi!");
        	}
        }else{
        	$player = $sender->getServer()->getPlayer($args[0]);
        	if($player === null){
        	    $sender->sendMessage(TextFormat::RED . "Hedef Oyuncu Aktif Değil veya Bulunamadı!");
        	    return true;
        	}
        
        	$player->getInventory()->addItem($pizza);
        	//$player->givePizza($mark);
        	$sender->sendMessage(TextFormat::GREEN . $player . " Adlı Oyuncuya Pizza Verildi!");
        }
        
        return true;
    }
}