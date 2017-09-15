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

use pocketmine\level\Level;
use pocketmine\entity\monster\Monster;
use pocketmine\entity\animal\Animal;
use pocketmine\level\Position;
use pocketmine\entity\BaseEntity;
use pocketmine\block\Liquid;
use pocketmine\math\Vector3;

class SpawnerCreature{

	const MAX_MOB_IN_CHUNK = 6;
	const MAX_ANIMAL_IN_CHUNK = 8;

	public static function generateEntity($server, $useAnimal, $useMonster){
		if(!$useAnimal && !$useMonster){
			return;
		}
		
		$level = $server->getDefaultLevel();
		$chunks = array();
		$freezeChunks = array();
		foreach($server->getOnlinePlayers() as $entityhuman){
			if($entityhuman->isAlive()){
				$chunkX = floor($entityhuman->getX() >> 4);
				$chunkZ = floor($entityhuman->getZ() >> 4);
				$radius = 3;
				for ($dx = -$radius; $dx <= $radius; $dx++){
					for ($dz = -$radius; $dz <= $radius; $dz++){
						if(abs($dz) == $radius || abs($dz) == $radius){
							if(rand(1, 8) == 3){
								$hash = Level::chunkHash($chunkX + $dx, $chunkZ + $dz);
								$chunks[$hash] = $hash;
							}
						} else {
							$hash = Level::chunkHash($chunkX + $dx, $chunkZ + $dz);
							$freezeChunks[$hash] = $hash;
						}
					}
				}
			}
		}
		
		foreach($freezeChunks as $freezeChunk){
			unset($chunks[$freezeChunk]);
		}
		
		$chunksClone = $chunks;
		$animalInChunk = array();
		$monsterInChunk = array();
		$animalCount = 0;
		$monsterCount = 0;
		foreach($level->getEntities() as $entity){
			if($entity instanceof Monster){
				$monsterCount++;
				for ($i = -2; $i <= 2; $i++){
					for ($j = -2; $j <= 2; $j++){
						$hash = Level::chunkHash(floor($entity->getX()) >> 4 + $i, floor($entity->getZ()) >> 4 + $j);
						if(!isset($monsterInChunk[$hash])){
							$monsterInChunk[$hash] = 1;
						}else{
							$monsterInChunk[$hash] ++;
						}
					}
				}
			}
			
			if($entity instanceof Animal){
				for ($i = -2; $i <= 2; $i++){
					for ($j = -2; $j <= 2; $j++){
						$hash = Level::chunkHash(floor($entity->getX()) >> 4 + $i, floor($entity->getZ()) >> 4 + $j);
						if(!isset($animalInChunk[$hash])){
							$animalInChunk[$hash] = 1;
						}else{
							$animalInChunk[$hash] ++;
						}
					}
				}
				
				$animalCount++;
			}
		}
		
		if($useAnimal){
			while ($animalCount < $server->getAnimalLimit() && count($chunks) > 0){
				$key = array_rand($chunks);
				$index = $chunks[$key];
				unset($chunks[$key]);
				if(isset($animalInChunk[$index]) && $animalInChunk[$index] >= self::MAX_ANIMAL_IN_CHUNK){
					continue;
				}
				
				$animals = array("Cow", "Pig", "Sheep", "Chicken", "Wolf", "Ocelot", "Mooshroom", "Rabbit", "IronGolem", "SnowGolem");
				$pos = self::getPosition($index, $level);
				if($pos){
					$animal = $animals[array_rand($animals)];
					if($animal == "Wolf"){
						$animal = $animals[array_rand($animals)];
					}
					
					BaseEntity::create($animal, $pos);
					$animalCount++;
				}
			}
		}
		
		$time = $level->getTime() % 30000;
		$isNight = $time > 16000 && $time < 29000;
		if($useMonster && $isNight){
			$chunks = $chunksClone;
			while ($monsterCount < $server->getMonsterLimit() && count($chunks) > 0){
				$key = array_rand($chunks);
				$index = $chunks[$key];
				unset($chunks[$key]);
				if(isset($monsterInChunk[$index]) && $monsterInChunk[$index] >= self::MAX_MOB_IN_CHUNK){
					continue;
				}
				
				$monsters = array("Zombie", "Skeleton", "Spider", "PigZombie", "Enderman", "CaveSpider", "ZombieVillager");
				$pos = self::getPosition($index, $level);
				if($pos){
					BaseEntity::create($monsters[array_rand($monsters)], $pos);
					$monsterCount++;
				}
			}
		}
	}

	private static function getPosition($index, $level){
		$chunkX = null;
		$chunkZ = null;
		Level::getXZ($index, $chunkX, $chunkZ);
		$chunk = $level->getChunk($chunkX, $chunkZ, false);
		if(!is_null($chunk) && $chunk->isPopulated()){
			$x = ($chunkX << 4) + rand(0, 15);
			$z = ($chunkZ << 4) + rand(0, 15);
			$y = $level->getHighestBlockAt($x, $z) + 2;
			if($level->getBlock(new Vector3($x, $y - 2, $z)) instanceof Liquid){
				return false;
			}
			
			return new Position($x, $y, $z, $level);
		}
		
		return false;
	}

}
