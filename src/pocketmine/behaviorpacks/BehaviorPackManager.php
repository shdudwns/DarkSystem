<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\behaviorpacks;

use pocketmine\Server;
use pocketmine\utils\Config;

class BehaviorPackManager{

	/** @var Server */
	private $server;

	/** @var string */
	private $path;

	/** @var Config */
	private $behaviorPacksConfig;

	/** @var bool */
	private $serverForceResources = false;

	/** @var BehaviorPack[] */
	private $behaviorPacks = [];

	/** @var BehaviorPack[] */
	private $uuidList = [];

	/**
	 * @param Server $server
	 * @param string $path
	 */
	public function __construct(Server $server, string $path){
		$this->server = $server;
		$this->path = $path;

		if(!file_exists($this->path)){
			mkdir($this->path);
		}elseif(!is_dir($this->path)){
			throw new \InvalidArgumentException("Behavior packs path $path exists and is not a directory");
		}

		if(!file_exists($this->path . "behavior_paketleri.yml")){
			file_put_contents($this->path . "behavior_paketleri.yml", file_get_contents($this->server->getFilePath() . "src/pocketmine/resources/behavior_paketleri.yml"));
		}

		$this->behaviorPacksConfig = new Config($this->path . "behavior_paketleri.yml", Config::YAML, []);

		$this->serverForceResources = (bool) $this->behaviorPacksConfig->get("force_behavior", false);

		foreach($this->behaviorPacksConfig->get("behavior_stack", []) as $pos => $pack){
			try{
				$packPath = $this->path . DIRECTORY_SEPARATOR . $pack;
				if(file_exists($packPath)){
					$newPack = null;
					//Detect the type of behavior pack.
					if(is_dir($packPath)){
						$this->server->getLogger()->warning("Skipped behavior entry $pack due to directory behavior packs currently unsupported");
					}else{
						$info = new \SplFileInfo($packPath);
						switch($info->getExtension()){
							case "zip":
							case "mcpack":
								$newPack = new ZippedBehaviorPack($packPath);
								break;
							default:
								$this->server->getLogger()->warning("Skipped behavior entry $pack due to format not recognized");
								break;
						}
					}

					if($newPack instanceof BehaviorPack){
						$this->behaviorPacks[] = $newPack;
						$this->uuidList[$newPack->getPackId()] = $newPack;
					}
				}else{
					$this->server->getLogger()->warning("Skipped behavior entry $pack due to file or directory not found");
				}
			}catch(\Throwable $e){
				$this->server->getLogger()->logException($e);
			}
		}

		$this->server->getLogger()->debug("Successfully loaded " . count($this->behaviorPacks) . " behavior packs");
	}

	/**
	 * Returns whether players must accept behavior packs in order to join.
	 * @return bool
	 */
	public function behaviorPacksRequired() : bool{
		return $this->serverForceResources;
	}

	/**
	 * Returns an array of behavior packs in use, sorted in order of priority.
	 * @return ResourcePack[]
	 */
	public function getBehaviorStack() : array{
		return $this->behaviorPacks;
	}

	/**
	 * Returns the behavior pack matching the specified UUID string, or null if the ID was not recognized.
	 *
	 * @param string $id
	 * @return ResourcePack|null
	 */
	public function getPackById(string $id){
		return $this->uuidList[$id] ?? null;
	}

	/**
	 * Returns an array of pack IDs for packs currently in use.
	 * @return string[]
	 */
	public function getPackIdList() : array{
		return array_keys($this->uuidList);
	}
}