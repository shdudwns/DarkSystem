<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine;

use pocketmine\event\TranslationContainer;
use pocketmine\utils\TextFormat;

abstract class AchievementHandler{
	
	public static $list = [
		"mineWood" => [
			"name" => "Odun Bulmak",
			"requires" => [
			],
		],
		"buildWorkBench" => [
			"name" => "Zanaat",
			"requires" => [
				"mineWood",
			],
		],
		"buildPickaxe" => [
			"name" => "Madencilik Zamanı!",
			"requires" => [
				"buildWorkBench",
			],
		],
		"buildFurnace" => [
			"name" => "Fırıncılık",
			"requires" => [
				"buildPickaxe",
			],
		],
		"acquireIron" => [
			"name" => "Gelişmeye İlk Adım",
			"requires" => [
				"buildFurnace",
			],
		],
		"buildHoe" => [
			"name" => "Tarım Zamanı!",
			"requires" => [
				"buildWorkBench",
			],
		],
		"makeBread" => [
			"name" => "Ekmek Yap",
			"requires" => [
				"buildHoe",
			],
		],
		"bakeCake" => [
			"name" => "Kek Yap",
			"requires" => [
				"buildHoe",
			],
		],
		"buildBetterPickaxe" => [
			"name" => "Gelişmek",
			"requires" => [
				"buildPickaxe",
			],
		],
		"buildSword" => [
			"name" => "Saldırı Zamanı!",
			"requires" => [
				"buildWorkBench",
			],
		],
		"diamonds" => [
			"name" => "Elmas Bulmak!",
			"requires" => [
				"acquireIron",
			],
		],
	];
	
	public static function broadcast(Player $player, $achievementId){
		
	}

	public static function add($achievementId, $achievementName, array $requires = []){
		if(!isset(Achievement::$list[$achievementId])){
			Achievement::$list[$achievementId] = [
				"name" => $achievementName,
				"requires" => $requires,
			];

			return true;
		}

		return false;
	}
	
}
