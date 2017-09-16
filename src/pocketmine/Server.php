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

use darksystem\DSPlayer;
use darksystem\DarkSystem;
use pocketmine\block\Block;
use pocketmine\ui\CustomUI;
use pocketmine\darkbot\DarkBot;
use pocketmine\darkbot\command\SpawnDarkBotCommand;
use pocketmine\command\CommandReader;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\command\SimpleCommandMap;
use pocketmine\entity\{Entity, Attribute, Effect, Arrow, BlazeFireball, Camera, Car, Item as DroppedItem, Egg, EnderCrystal, EnderPearl, FallingSand, FishingHook, GhastFireball, LeashKnot, Lightning, Minecart, MinecartChest, MinecartHopper, MinecartTNT, Painting, PrimedTNT, Snowball, ThrownExpBottle, ThrownPotion, XPOrb, Herobrine, Human, Bat, BlueWitherSkull, Boat, Dragon, Donkey, ElderGuardian, EnderDragon, Endermite, EvocationFangs, Giant, Guardian, Horse, Husk, LavaSlime, Llama, MagmaCube, Mule, PolarBear, Shulker, ShulkerBullet, Slime, SkeletonHorse, Stray, Squid, Vex, Villager, Vindicator, Witch, Wither, WitherSkeleton, ZombieHorse};
use pocketmine\event\HandlerList;
use pocketmine\event\level\LevelInitEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\event\Timings;
use pocketmine\event\TimingsHandler;
use pocketmine\inventory\CraftingManager;
use pocketmine\inventory\InventoryType;
use pocketmine\inventory\Recipe;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\enchantment\{Enchantment, EnchantmentLevelTable};
use pocketmine\item\Item;
use pocketmine\language\Language;
use pocketmine\level\format\anvil\Anvil;
use pocketmine\level\format\mcregion\McRegion;
use pocketmine\level\format\LevelProviderManager;
use pocketmine\level\Level;
use pocketmine\metadata\EntityMetadataStore;
use pocketmine\metadata\LevelMetadataStore;
use pocketmine\metadata\PlayerMetadataStore;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\Network;
use pocketmine\network\CompressBatchedTask;
use pocketmine\network\protocol\Info as ProtocolInfo;
use pocketmine\network\protocol\BatchPacket;
use pocketmine\network\protocol\CraftingDataPacket;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\PlayerListPacket;
use pocketmine\network\query\QueryHandler;
use pocketmine\network\RakLibInterface;
use pocketmine\network\rcon\RCON;
use pocketmine\network\SourceInterface;
use pocketmine\permission\BanList;
use pocketmine\permission\DefaultPermissions;
use pocketmine\plugin\PharPluginLoader;
use pocketmine\plugin\ScriptPluginLoader;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginLoadOrder;
use pocketmine\plugin\PluginManager;
use pocketmine\resourcepacks\ResourcePackManager;
use pocketmine\behaviorpacks\BehaviorPackManager;
use pocketmine\scheduler\ServerScheduler;
use pocketmine\tile\{Tile, Beacon, Bed, BrewingStand, Cauldron, Chest, CommandBlock, Dispenser, DLDetector, Dropper, EnchantTable, EnderChest, FlowerPot, Furnace, Hopper, ItemFrame, MobSpawner, Sign, Skull};
use pocketmine\utils\Binary;
use pocketmine\utils\Cache;
use pocketmine\utils\Config;
use pocketmine\utils\LevelException;
use pocketmine\utils\MainLogger;
use pocketmine\utils\MetadataConvertor;
use pocketmine\utils\ServerException;
use pocketmine\utils\Terminal;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\TextWrapper;
use pocketmine\utils\Utils;
use pocketmine\utils\UUID;
use pocketmine\utils\VersionString;
use pocketmine\level\generator\biome\Biome;
use pocketmine\level\generator\nether\Nether;
use pocketmine\level\generator\ender\Ender;
use pocketmine\level\generator\normal\Normal;
use pocketmine\scheduler\FileWriteTask;
use pocketmine\entity\animal\walking\{Chicken, Cow, Mooshroom, Ocelot, Pig, Rabbit, Sheep};
use pocketmine\entity\monster\flying\{Blaze, Ghast};
use pocketmine\entity\monster\walking\{CaveSpider, Creeper, Enderman, IronGolem, PigZombie, Silverfish, Skeleton, SnowGolem, Spider, Wolf, Zombie, ZombieVillager};
use pocketmine\entity\projectile\FireBall;

//class Server{
class Server extends DarkSystem{
	
	const BROADCAST_CHANNEL_ADMINISTRATIVE = "pocketmine.broadcast.admin";
	const BROADCAST_CHANNEL_USERS = "pocketmine.broadcast.user";
	
	/** @var Server */
	private static $instance = null;
	
	/** @var BanList */
	private $banByName = null;

	/** @var BanList */
	private $banByIP = null;
	
	/** @var BanList */
	private $banByCID = null;
	
	/** @var Config */
	private $operators = null;

	/** @var Config */
	private $whitelist = null;

	/** @var bool */
	private $isRunning = true;

	private $hasStopped = false;
	
	private $pluginMgr = null;
	
	private $scheduler = null;
	
	private $generationMgr = null;
	
	private $tickCounter;
	private $nextTick = 0;
	private $tickAverage = [20, 20, 20, 20, 20];
	private $useAverage = [20, 20, 20, 20, 20];
	
	private $knsol;
	
	private $console = null;
	private $consoleThreaded;
	
	private $cmdMap = null;
	
	private $craftingMgr;

	/** @var ConsoleCommandSender */
	private $consoleSender;

	/** @var int */
	private $maxPlayers;

	/** @var bool */
	private $autoSave;
	
	/** @var bool */
	private $autoGenerate;
	
	/** @var bool */
	private $savePlayerData;

	/** @var RCON */
	private $rcon;

	/** @var EntityMetadataStore */
	private $entityMetadata;

	/** @var PlayerMetadataStore */
	private $playerMetadata;

	/** @var LevelMetadataStore */
	private $levelMetadata;

	/** @var Network */
	private $network;

	private $networkCompressionAsync = true;
	public $networkCompressionLevel = 6;
	
	private $autoSaveTicker = 0;
	private $autoSaveTicks = 6000;
	
	private $language;
	
	private $forceLanguage = true;
	
	private $serverID;
	
	private $autoloader;
	private $filePath;
	private $dataPath;
	private $pluginPath;

	private $lastSendUsage = null;

	/** @var QueryHandler */
	private $queryHandler;

	/** @var QueryRegenerateEvent */
	private $queryRegenerateTask = null;

	/** @var Config */
	private $properties;
	
	private $propertyCache = [];
	
	/** @var Config */
	private $config;

	/** @var Config */
	private $softConfig;

	/** @var Player[] */
	private $players = [];

	/** @var Player[] */
	private $playerList = [];

	private $identifiers = [];

	/** @var Level[] */
	private $levels = [];

	/** @var Level */
	private $levelDefault = null;
	
	private $useAnimal;
	private $animalLimit;
	private $useMonster ;
	private $monsterLimit;
	
	public $packetMgr = null;
	
	private $spawnedEntity = [];
	
	private $unloadLevelQueue = [];
	
	public $keepInventory = false;
	public $netherEnabled = false;
	public $netherName = "cehennem";
	public $weatherRandomDurationMin = 6000;
	public $weatherRandomDurationMax = 12000;
	public $lightningTime = 200;
	public $lightningFire = false;
	public $endEnabled = false;
    public $endName = "end";
    public $redstoneEnabled = false;
	public $checkMovement = true;
	public $antiFly = true;
	public $allowInstabreak = false;
	public $forceResources = false;
	public $resourceStack = [];
	public $forceBehavior = false;
	public $behaviorStack = [];
	
	public function addSpawnedEntity($entity){
		if($entity instanceof Player){
			return;
		}
		
		$this->spawnedEntity[$entity->getId()] = $entity;
	}

	public function removeSpawnedEntity($entity){
		unset($this->spawnedEntity[$entity->getId()]);
	}
	
	public function isUseAnimal(){
		return $this->useAnimal;
	}

	public function getAnimalLimit(){
		return $this->animalLimit;
	}

	public function isUseMonster(){
		return $this->useMonster;
	}

	public function getMonsterLimit(){
		return $this->monsterLimit;
	}
	
	public function getName(){
		return "DarkSystem";
	}
	
	public function isRunning(){
		return $this->isRunning === true;
	}
	
	public function getDarkSystemVersion(){
		return \pocketmine\VERSION;
	}
	
	public function getPocketMineVersion(){
		return \pocketmine\VERSION;
	}
	
	public function getFormattedVersion($prefix = ""){
		return (\pocketmine\VERSION !== ""? $prefix . \pocketmine\VERSION : "");
	}
	
	public function getCodename(){
		return \pocketmine\CODENAME;
	}
	
	public function getCreator(){
		return \pocketmine\CREATOR;
	}
	
	public function getTag(){
		return \pocketmine\TAG;
	}
	
	public function getVersion(){
		return ProtocolInfo::MINECRAFT_VERSION;
	}
	
	public function getApiVersion(){
		return \pocketmine\API_VERSION;
	}
	
	public function getDarkBotVersion(){
		return \pocketmine\DARKBOT_VERSION;
	}
	
	public function getDarkBotAI(){
		return SpawnDarkBotCommand::$darkbot;
	}
	
	public function getFilePath(){
		return $this->filePath;
	}
	
	public function getDataPath(){
		return $this->dataPath;
	}
	
	public function getPluginPath(){
		return $this->pluginPath;
	}
	
	public function getMaxPlayers(){
		return $this->maxPlayers;
	}
	
	public function getPort(){
		return $this->getConfigInt("server-port", 19132);
	}
	
	public function getViewDistance(){
		return 72;
	}
	
	public function getIp(){
		return $this->getConfigString("server-ip", "0.0.0.0");
	}
	
	public function getServerUniqueId(){
		return $this->serverID;
	}
	
	public function getServerName(){
		return $this->getConfigString("motd", "DarkSystem Sunucusu");
	}
	
	public function getAutoSave(){
		return $this->autoSave;
	}
	
	public function setAutoSave($value){
		$this->autoSave = (bool) $value;
		foreach($this->levels as $l){
			$l->setAutoSave($this->autoSave);
		}
	}
	
	public function getAutoGenerate(){
		return $this->autoGenerate;
	}
	
	public function setAutoGenerate($value){
		$this->autoGenerate = (bool) $value;
	}
	
	public function getSavePlayerData(){
		return $this->savePlayerData;
	}
	
	public function setSavePlayerData($value){
		$this->savePlayerData = (bool) $value;
	}
	
	public function getLevelType(){
		return $this->getConfigString("level-type", "DEFAULT");
	}
	
	public function getGenerateStructures(){
		return $this->getConfigBoolean("generate-structures", true);
	}
	
	public function getGamemode(){
		return $this->getConfigInt("gamemode", 0) & 0b11;
	}
	
	public function getForceGamemode(){
		return $this->getConfigBoolean("force-gamemode", false);
	}
	
	public static function getGamemodeString($mode){
		switch((int) $mode){
			case Player::SURVIVAL:
				return "%gameMode.survival";
			case Player::CREATIVE:
				return "%gameMode.creative";
			case Player::ADVENTURE:
				return "%gameMode.adventure";
			case Player::SPECTATOR:
				return "%gameMode.spectator";
		}

		return "BILINMEYEN";
	}
	
	public static function getGamemodeFromString($str){
		switch(strtolower(trim($str))){
			case (string) Player::SURVIVAL:
			case "survival":
			case "s":
				return Player::SURVIVAL;
			case (string) Player::CREATIVE:
			case "creative":
			case "c":
				return Player::CREATIVE;
			case (string) Player::ADVENTURE:
			case "adventure":
			case "a":
				return Player::ADVENTURE;
			case (string) Player::SPECTATOR:
			case "spectator":
			case "view":
			case "v":
				return Player::SPECTATOR;
		}
		
		return -1;
	}
	
	public static function getDifficultyFromString($str){
		switch(strtolower(trim($str))){
			case "0":
			case "peaceful":
			case "p":
				return 0;
			case "1":
			case "easy":
			case "e":
				return 1;
			case "2":
			case "normal":
			case "n":
				return 2;
			case "3":
			case "hard":
			case "h":
				return 3;
		}
		
		return -1;
	}
	
	public function getDifficulty(){
		return $this->getConfigInt("difficulty", 1);
	}
	
	public function hasWhitelist(){
		return $this->getConfigBoolean("white-list", false);
	}
	
	public function getSpawnRadius(){
		return $this->getConfigInt("spawn-protection", 16);
	}
	
	public function getAllowFlight(){
		return $this->getConfigBoolean("allow-flight", false);
	}
	
	public function isHardcore(){
		return $this->getConfigBoolean("hardcore", false);
	}
	
	public function getDefaultGamemode(){
		return $this->getConfigInt("gamemode", 0) & 0b11;
	}
	
	public function getMotd(){
		return $this->getConfigString("motd", "DarkSystem Sunucusu");
	}
	
	public function getdbot(){
		return $this->dbot;
	}
	
	public function getDarkBot(){
		return $this->dbot;
	}
	
	public function getDarkBotPrefix(){
		return DarkBot::PREFIX;
	}
	
	/**
	 * @return \ClassLoader
	 */
	public function getLoader(){
		return $this->autoloader;
	}

	/**
	 * @return \AttachableThreadedLogger
	 */
	public function getLogger(){
		return $this->konsol;
	}

	/**
	 * @return EntityMetadataStore
	 */
	public function getEntityMetadata(){
		return $this->entityMetadata;
	}

	/**
	 * @return PlayerMetadataStore
	 */
	public function getPlayerMetadata(){
		return $this->playerMetadata;
	}

	/**
	 * @return LevelMetadataStore
	 */
	public function getLevelMetadata(){
		return $this->levelMetadata;
	}

	/**
	 * @return PluginManager
	 */
	public function getPluginManager(){
		return $this->pluginMgr;
	}

	/**
	 * @return CraftingManager
	 */
	public function getCraftingManager(){
		return $this->craftingMgr;
	}
	
	/**
	 * @return ResourcePackManager
	 */
	public function getResourcePackManager(){
		return $this->resourceMgr;
	}
	
	/**
	 * @return BehaviorPackManager
	 */
	public function getBehaviorPackManager(){
		return $this->behaviorMgr;
	}
	
	/**
	 * @return ServerScheduler
	 */
	public function getScheduler(){
		return $this->scheduler;
	}
	
	public function getTick(){
		return $this->tickCounter;
	}
	
	public function getTicksPerSecond(){
		return round(array_sum($this->tickAverage) / count($this->tickAverage), 2);
	}
	
	public function getTickUsage(){
		return round((array_sum($this->useAverage) / count($this->useAverage)) * 100, 2);
	}
	
	/**
	 * @param     $address
	 * @param int $timeout
	 */
	public function blockAddress($address, $timeout = 300){
		$this->network->blockAddress($address, $timeout);
	}

	/**
	 * @param $address
	 * @param $port
	 * @param $payload
	 */
	public function sendPacket($address, $port, $payload){
		$this->network->sendPacket($address, $port, $payload);
	}

	/**
	 * @return SourceInterface[]
	 */
	public function getInterfaces(){
		return $this->network->getInterfaces();
	}

	/**
	 * @param SourceInterface $interface
	 */
	public function addInterface(SourceInterface $interface){
		$this->network->registerInterface($interface);
	}

	/**
	 * @param SourceInterface $interface
	 */
	public function removeInterface(SourceInterface $interface){
		$interface->shutdown();
		$this->network->unregisterInterface($interface);
	}

	/**
	 * @return SimpleCommandMap
	 */
	public function getCommandMap(){
		return $this->cmdMap;
	}

	/**
	 * @return Player[]
	 */
	public function getOnlinePlayers(){
		return $this->playerList;
	}

	public function addRecipe(Recipe $recipe){
		$this->craftingMgr->registerRecipe($recipe);
	}
	
	public function clearChat(){
		foreach($this->getOnlinePlayers() as $p){
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
			$p->sendMessage(" ");
		}
	}
	
	/**
	 * @param string $name
	 *
	 * @return OfflinePlayer|Player
	 */
	public function getOfflinePlayer($name){
		$name = strtolower($name);
		$result = $this->getPlayerExact($name);
		if($result === null){
			$result = new OfflinePlayer($this, $name);
		}

		return $result;
	}

	/**
	 * @param string $name
	 *
	 * @return Compound
	 */
	public function getOfflinePlayerData($name){
		$name = strtolower($name);
		$path = $this->getDataPath() . "oyuncular/";
		if(file_exists($path . "$name.dat")){
			try{
				$nbt = new NBT(NBT::BIG_ENDIAN);
				$nbt->readCompressed(file_get_contents($path . "$name.dat"));
				return $nbt->getData();
			}catch(\Exception $e){
				rename($path . "$name.dat", $path . "$name.dat.bak");
				$this->konsol->notice($this->getLanguage()->translateString("pocketmine.data.playerCorrupted", [$name]));
			}
		}else{
			$this->konsol->notice($this->getLanguage()->translateString("pocketmine.data.playerNotFound", [$name]));
		}
		
		$spawn = $this->getDefaultLevel()->getSafeSpawn();
		$nbt = new Compound("", [
			new LongTag("firstPlayed", floor(microtime(true) * 1000)),
			new LongTag("lastPlayed", floor(microtime(true) * 1000)),
			new Enum("Pos", [
				new DoubleTag(0, $spawn->x),
				new DoubleTag(1, $spawn->y),
				new DoubleTag(2, $spawn->z)
			]),
			new StringTag("Level", $this->getDefaultLevel()->getName()),
			new Enum("Inventory", []),
			new Enum("EnderChestInventory", []),
			new Compound("Achievements", []),
			new IntTag("playerGameType", $this->getGamemode()),
			new Enum("Motion", [
				new DoubleTag(0, 0.0),
				new DoubleTag(1, 0.0),
				new DoubleTag(2, 0.0)
			]),
			new Enum("Rotation", [
				new FloatTag(0, 0.0),
				new FloatTag(1, 0.0)
			]),
			new FloatTag("FallDistance", 0.0),
			new ShortTag("Fire", 0),
			new ShortTag("Air", 300),
			new ByteTag("OnGround", 1),
			new ByteTag("Invulnerable", 0),
			new StringTag("NameTag", $name),
		]);
		$nbt->Pos->setTagType(NBT::TAG_Double);
		$nbt->Inventory->setTagType(NBT::TAG_Compound);
		$nbt->EnderChestInventory->setTagType(NBT::TAG_Compound);
		$nbt->Motion->setTagType(NBT::TAG_Double);
		$nbt->Rotation->setTagType(NBT::TAG_Float);
		
		return $nbt;
	}

	/**
	 * @param string   $name
	 * @param Compound $nbtTag
	 */
	public function saveOfflinePlayerData($name, Compound $nbtTag, $async = false){
		$nbt = new NBT(NBT::BIG_ENDIAN);
		try{
			$nbt->setData($nbtTag);
			if($async){
				$this->scheduler->scheduleAsyncTask(new FileWriteTask($this->getDataPath() . "oyuncular/" . strtolower($name) . ".dat", $nbt->writeCompressed()));
			}else{
				file_put_contents($this->getDataPath() . "oyuncular/" . strtolower($name) . ".dat", $nbt->writeCompressed());
			}
		}catch(\Exception $e){
			$this->konsol->critical($this->getLanguage()->translateString("pocketmine.data.saveError", [$name, $e->getMessage()]));
			if(\pocketmine\DEBUG > 1 && $this->konsol instanceof MainLogger){
				$this->konsol->logException($e);
			}
		}
	}

	/**
	 * @param string $name
	 *
	 * @return Player|null
	 */
	public function getPlayer($name){
		$found = null;
		$name = strtolower($name);
		$delta = PHP_INT_MAX;
		foreach($this->getOnlinePlayers() as $p){
			$playerName = strtolower($p->getName());
			if(strpos($playerName, $name) === 0){
				$curDelta = strlen($playerName) - strlen($name);
				if($curDelta < $delta){
					$found = $p;
					$delta = $curDelta;
				}
				
				if($curDelta == 0){
					break;
				}
			}
		}

		return $found;
	}

	/**
	 * @param string $name
	 *
	 * @return Player
	 */
	public function getPlayerExact($name){
		$name = strtolower($name);
		foreach($this->getOnlinePlayers() as $p){
			if(strtolower($p->getName()) === $name){
				return $p;
			}
		}

		return null;
	}

	/**
	 * @param string $partialName
	 *
	 * @return Player[]
	 */
	public function matchPlayer($partialName){
		$partialName = strtolower($partialName);
		$matchedPlayers = [];
		foreach($this->getOnlinePlayers() as $p){
			$playerName = strtolower($p->getName());
			if($playerName === $partialName){
				$matchedPlayers = [$p];
				break;
			}elseif(strpos($playerName, $partialName) !== false){
				$matchedPlayers[] = $p;
			}
		}

		return $matchedPlayers;
	}
	
	public function removePlayer(Player $player){
		if(isset($this->identifiers[$hash = spl_object_hash($player)])){
			$identifier = $this->identifiers[$hash];
			unset($this->players[$identifier]);
			unset($this->identifiers[$hash]);
			return;
		}

		foreach($this->players as $identifier => $p){
			if($player === $p){
				unset($this->players[$identifier]);
				unset($this->identifiers[spl_object_hash($player)]);
				break;
			}
		}
	}

	/**
	 * @return Level[]
	 */
	public function getLevels(){
		return $this->levels;
	}

	/**
	 * @return Level
	 */
	public function getDefaultLevel(){
		return $this->levelDefault;
	}

	/**
	 * @param Level $level
	 */
	public function setDefaultLevel($level){
		if($level === null || ($this->isLevelLoaded($level->getFolderName()) && $level !== $this->levelDefault)){
			$this->levelDefault = $level;
		}
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isLevelLoaded($name){
		return $this->getLevelByName($name) instanceof Level;
	}

	/**
	 * @param int $levelId
	 *
	 * @return Level
	 */
	public function getLevel($levelId){
		if(isset($this->levels[$levelId])){
			return $this->levels[$levelId];
		}

		return null;
	}

	/**
	 * @param $name
	 *
	 * @return Level
	 */
	public function getLevelByName($name){
		foreach($this->levels as $l){
			if($l->getFolderName() === $name){
				return $l;
			}
		}

		return null;
	}

	/**
	 * @param Level $level
	 * @param bool  $forceUnload
	 *
	 * @return bool
	 */
	public function unloadLevel(Level $level, $forceUnload = false, $direct = false){
		if($direct){
			if($level->unload($forceUnload) === true){
				unset($this->levels[$level->getId()]);
				return true;
			}
		}else{
			$this->unloadLevelQueue[$level->getId()] = ['level' => $level, 'force' => $forceUnload];
		}

		return false;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 *
	 * @throws LevelException
	 */
	public function loadLevel($name){
		if(trim($name) === ""){
			throw new LevelException("Geçersiz Dünya İsmi!");
		}
		
		if($this->isLevelLoaded($name)){
			return true;
		}elseif(!$this->isLevelGenerated($name)){
			$this->konsol->notice($this->getLanguage()->translateString("pocketmine.level.notFound", [$name]));
			return false;
		}

		$path = $this->getDataPath() . "dunyalar/" . $name . "/";

		$provider = LevelProviderManager::getProvider($path);

		if($provider === null){
			$this->konsol->error($this->getLanguage()->translateString("pocketmine.level.loadError", [$name, "Bilinmeyen Harita Yükleyici"]));
			return false;
		}
		
		try{
			$level = new Level($this, $name, $path, $provider);
		}catch(\Exception $e){
			$this->konsol->error($this->getLanguage()->translateString("pocketmine.level.loadError", [$name, $e->getMessage()]));
			if($this->konsol instanceof MainLogger){
				$this->konsol->logException($e);
			}
			return false;
		}

		$this->levels[$level->getId()] = $level;

		$level->initLevel();

		$this->pluginMgr->callEvent(new LevelLoadEvent($level));
		
		return true;
	}

	/**
	 * @param string $name
	 * @param int    $seed
	 * @param array  $options
	 *
	 * @return bool
	 */
	public function generateLevel($name, $seed = null, $options = []){
		if(trim($name) === "" || $this->isLevelGenerated($name)){
			return false;
		}

		$seed = $seed === null ? Binary::readInt(@Utils::getRandomBytes(4, false)) : (int) $seed;

		if(($provider = LevelProviderManager::getProviderByName($providerName = $this->getProperty("level-settings.default-format", "anvil"))) === null){
			$provider = LevelProviderManager::getProviderByName($providerName = "anvil");
		}

		try{
			$path = $this->getDataPath() . "dunyalar/" . $name . "/";
			$provider::generate($path, $name, $seed, $options);

			$level = new Level($this, $name, $path, $provider);
			$this->levels[$level->getId()] = $level;

			$level->initLevel();
		}catch(\Exception $e){
			$this->konsol->error("Could not generate level \"" . $name . "\": " . $e->getMessage());
			if($this->konsol instanceof MainLogger){
				$this->konsol->logException($e);
			}
			
			return false;
		}

		$this->pluginMgr->callEvent(new LevelInitEvent($level));
		$this->pluginMgr->callEvent(new LevelLoadEvent($level));

		if($this->getAutoGenerate()){
			$centerX = $level->getSpawnLocation()->getX() >> 4;
			$centerZ = $level->getSpawnLocation()->getZ() >> 4;

			$order = [];

			for($X = -3; $X <= 3; ++$X){
				for($Z = -3; $Z <= 3; ++$Z){
					$distance = $X ** 2 + $Z ** 2;
					$chunkX = $X + $centerX;
					$chunkZ = $Z + $centerZ;
					$index = Level::chunkHash($chunkX, $chunkZ);
					$order[$index] = $distance;
				}
			}

			asort($order);

			foreach($order as $index => $distance){
				Level::getXZ($index, $chunkX, $chunkZ);
				$level->generateChunk($chunkX, $chunkZ, true);
			}
		}

		return true;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isLevelGenerated($name){
		if(trim($name) === ""){
			return false;
		}
		
		$path = $this->getDataPath() . "dunyalar/" . $name . "/";
		if(!($this->getLevelByName($name) instanceof Level)){
			if(LevelProviderManager::getProvider($path) === null){
				return false;
			}
		}

		return true;
	}
	
	/**
	 * @param string $variable
	 * @param string $defaultValue
	 *
	 * @return string
	 */
	public function getConfigString($variable, $defaultValue = ""){
		$v = getopt("", ["$variable::"]);
		if(isset($v[$variable])){
			return (string) $v[$variable];
		}

		return $this->properties->exists($variable) ? $this->properties->get($variable): $defaultValue;
	}

	/**
	 * @param string $variable
	 * @param mixed  $defaultValue
	 *
	 * @return mixed
	 */
	public function getProperty($variable, $defaultValue = null){
		if(!array_key_exists($variable, $this->propertyCache)){
			$v = getopt("", ["$variable::"]);
			if(isset($v[$variable])){
				$this->propertyCache[$variable] = $v[$variable];
			}else{
				$this->propertyCache[$variable] = $this->config->getNested($variable);
			}
		}

		return $this->propertyCache[$variable] === null ? $defaultValue : $this->propertyCache[$variable];
	}

	/**
	 * @param string $variable
	 * @param string $value
	 */
	public function setConfigString($variable, $value){
		$this->properties->set($variable, $value);
	}

	/**
	 * @param string $variable
	 * @param int    $defaultValue
	 *
	 * @return int
	 */
	public function getConfigInt($variable, $defaultValue = 0){
		$v = getopt("", ["$variable::"]);
		if(isset($v[$variable])){
			return (int) $v[$variable];
		}

		return $this->properties->exists($variable) ? (int) $this->properties->get($variable): (int) $defaultValue;
	}

	/**
	 * @param string $variable
	 * @param int    $value
	 */
	public function setConfigInt($variable, $value){
		$this->properties->set($variable, (int) $value);
	}

	/**
	 * @param string  $variable
	 * @param boolean $defaultValue
	 *
	 * @return boolean
	 */
	public function getConfigBoolean($variable, $defaultValue = false){
		$v = getopt("", ["$variable::"]);
		if(isset($v[$variable])){
			$value = $v[$variable];
		}else{
			$value = $this->properties->exists($variable) ? $this->properties->get($variable): $defaultValue;
		}

		if(is_bool($value)){
			return $value;
		}
		
		switch(strtolower($value)){
			case "on":
			case "true":
			case "1":
			case "yes":
				return true;
		}

		return false;
	}

	/**
	 * @param string $variable
	 * @param bool   $value
	 */
	public function setConfigBool($variable, $value){
		$this->properties->set($variable, $value == true ? "1" : "0");
	}

	/**
	 * @param string $name
	 *
	 * @return PluginIdentifiableCommand
	 */
	public function getPluginCommand($name){
		if(($command = $this->cmdMap->getCommand($name)) instanceof PluginIdentifiableCommand){
			return $command;
		}else{
			return null;
		}
	}

	/**
	 * @return BanList
	 */
	public function getNameBans(){
		return $this->banByName;
	}

	/**
	 * @return BanList
	 */
	public function getIPBans(){
		return $this->banByIP;
	}
	
	public function getCIDBans(){
		return $this->banByCID;
	}
	
	/**
	 * @param string $name
	 */
	public function addOp($name){
		$this->operators->set(strtolower($name), true);
		if(($player = $this->getPlayerExact($name)) instanceof Player){
			$player->recalculatePermissions();
		}
		
		$this->operators->save();
	}

	/**
	 * @param string $name
	 */
	public function removeOp($name){
		$this->operators->remove(strtolower($name));
		if(($player = $this->getPlayerExact($name)) instanceof Player){
			$player->recalculatePermissions();
		}
		
		$this->operators->save();
	}

	/**
	 * @param string $name
	 */
	public function addWhitelist($name){
		$this->whitelist->set(strtolower($name), true);
		$this->whitelist->save();
	}

	/**
	 * @param string $name
	 */
	public function removeWhitelist($name){
		$this->whitelist->remove(strtolower($name));
		$this->whitelist->save();
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isWhitelisted($name){
		return !$this->hasWhitelist() || $this->operators->exists($name, true) || $this->whitelist->exists($name, true);
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isOp($name){
		return $this->operators->exists($name, true);
	}

	/**
	 * @return Config
	 */
	public function getWhitelisted(){
		return $this->whitelist;
	}

	/**
	 * @return Config
	 */
	public function getOps(){
		return $this->operators;
	}

	public function reloadWhitelist(){
		$this->whitelist->reload();
	}

	/**
	 * @return string[]
	 */
	public function getCommandAliases(){
		$section = $this->getProperty("aliases");
		$result = [];
		if(is_array($section)){
			foreach($section as $key => $value){
				$commands = [];
				if(is_array($value)){
					$commands = $value;
				}else{
					$commands[] = $value;
				}

				$result[$key] = $commands;
			}
		}

		return $result;
	}
	
	public function getCrashPath(){
		return $this->dataPath . "cokme-arsivleri/";
	}
	
	public static function getInstance(){
		return Server::$instance;
	}
	
	function curl($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

	public static function microSleep(int $microseconds){
		Server::$sleeper->synchronized(function(int $ms){
			Server::$sleeper->wait($ms);
		}, $microseconds);
	}

	public function loadAdvancedConfig(){
		$this->weatherEnabled = $this->getAdvancedProperty("level.weather", false);
		$this->foodEnabled = $this->getAdvancedProperty("player.hunger", true);
		$this->expEnabled = $this->getAdvancedProperty("player.experience", true);
		$this->keepInventory = $this->getAdvancedProperty("player.keep-inventory", false);
		$this->keepExperience = $this->getAdvancedProperty("player.keep-experience", false);
		$this->netherEnabled = $this->getAdvancedProperty("level.allow-nether", false);
		$this->netherName = $this->getAdvancedProperty("level.level-name", "nether");
		$this->endEnabled = $this->getAdvancedProperty("level.allow-end", false);
        $this->endName = $this->getAdvancedProperty("level.end-level-name", "end");
        $this->redstoneEnabled = $this->getAdvancedProperty("redstone.enable", false);
		$this->weatherRandomDurationMin = $this->getAdvancedProperty("level.weather-random-duration-min", 6000);
		$this->weatherRandomDurationMax = $this->getAdvancedProperty("level.weather-random-duration-max", 12000);
		$this->lightningTime = $this->getAdvancedProperty("level.lightning-time", 200);
		$this->lightningFire = $this->getAdvancedProperty("level.lightning-fire", false);
		$this->autoClearInv = $this->getAdvancedProperty("player.auto-clear-inventory", true);
		$this->asyncChunkRequest = $this->getAdvancedProperty("server.async-chunk-request", true);
		$this->limitedCreative = $this->getAdvancedProperty("server.limited-creative", true);
		$this->chunkRadius = $this->getAdvancedProperty("player.chunk-radius", -1);
		$this->allowSplashPotion = $this->getAdvancedProperty("server.allow-splash-potion", true);
		$this->fireSpread = $this->getAdvancedProperty("level.fire-spread", false);
		$this->advancedCommandSelector = $this->getAdvancedProperty("server.advanced-command-selector", false);
		$this->anvilEnabled = $this->getAdvancedProperty("enchantment.enable-anvil", true);
		$this->enchantingTableEnabled = $this->getAdvancedProperty("enchantment.enable-enchanting-table", true);
		$this->countBookshelf = $this->getAdvancedProperty("enchantment.count-bookshelf", false);
		$this->allowInventoryCheats = $this->getAdvancedProperty("inventory.allow-cheats", false);
		$this->checkMovement = $this->getAdvancedProperty("anticheat.check-movement", true);
		$this->allowInstabreak = $this->getAdvancedProperty("anticheat.allow-instabreak", true);
		$this->antiFly = $this->getAdvancedProperty("anticheat.anti-fly", true);
		$this->folderpluginloader = $this->getAdvancedProperty("developer.folder-plugin-loader", false);
		$this->useAnimal = $this->getAdvancedProperty("spawn-animals", false);
		$this->animalLimit = $this->getAdvancedProperty("animals-limit", 0);
		$this->useMonster = $this->getAdvancedProperty("spawn-mobs", false);
		$this->monsterLimit = $this->getAdvancedProperty("mobs-limit", 0);
		$this->forceResources = $this->getAdvancedProperty("packs.force-resources", false);
		$this->resourceStack = $this->getAdvancedProperty("packs.resource-stack", []);
		$this->forceBehavior = $this->getAdvancedProperty("packs.force-behavior", false);
		$this->behaviorStack = $this->getAdvancedProperty("packs.behavior-stack", []);
	}
	
	public function getBuild(){
		return $this->version->getBuild();
	}

	public function getGameVersion(){
		return $this->version->getRelease();
	}
	
	public function __construct(\ClassLoader $autoloader, \ThreadedLogger $knsol, $filePath, $dataPath, $pluginPath, $defaultLang = "Bilinmeyen"){
		Server::$instance = $this;
		$this->autoloader = $autoloader;
		$this->konsol = $knsol;
		$this->filePath = $filePath;
		$this->dbot = new DarkBot($this);
		try{
			if(!file_exists($dataPath . "dunyalar/")){
				mkdir($dataPath . "dunyalar/", 0777);
			}

			if(!file_exists($dataPath . "oyuncular/")){
				mkdir($dataPath . "oyuncular/", 0777);
			}
			
			if(!file_exists($dataPath . "cokme-arsivleri/")){
				mkdir($dataPath . "cokme-arsivleri/", 0777);
			}
			
			if(!file_exists($dataPath . "oyuncu-basarimlari/")){
				mkdir($dataPath . "oyuncu-basarimlari/", 0777);
			}
			
			if(!file_exists($pluginPath)){
				mkdir($pluginPath, 0777);
			}
			
			if(\Phar::running(true) === ""){
			   $packages = "src";
			}else{
				$packages = "phar";
			}

			$this->dataPath = realpath($dataPath) . DIRECTORY_SEPARATOR;
			$this->pluginPath = realpath($pluginPath) . DIRECTORY_SEPARATOR;

			if(!file_exists($this->dataPath . "pocketmine.yml")){
				$content1 = file_get_contents($this->filePath . "src/pocketmine/resources/pocketmine.yml");
				@file_put_contents($this->dataPath . "pocketmine.yml", $content1);
			}
			
			if(!file_exists($this->dataPath . "pocketmine-advanced.yml")){
				$content2 = file_get_contents($this->filePath . "src/pocketmine/resources/pocketmine-advanced.yml");
				@file_put_contents($this->dataPath . "pocketmine-advanced.yml", $content2);
			}
			
		    $this->softConfig = new Config($this->dataPath . "pocketmine-advanced.yml", Config::YAML, []);
		
			if(!is_dir($this->pluginPath . "DarkSystem")){
				mkdir($this->pluginPath . "DarkSystem");
			}
			
			$this->config = new Config($configPath = $this->dataPath . "pocketmine.yml", Config::YAML, []);
			$this->cmdReader = new CommandReader($knsol);
			$this->properties = new Config($this->dataPath . "sunucu.properties", Config::PROPERTIES, [
				"motd" => "DarkSystem Sunucusu",
				"server-ip" => "0.0.0.0",
				"server-port" => 19132,
				"memory-limit" => "256M",
				"white-list" => false,
				"announce-player-achievements" => false,
				"spawn-protection" => 16,
				"max-players" => 100,
				"allow-flight" => false,
				"spawn-animals" => true,
				"animals-limit" => 0,
				"spawn-mobs" => true,
				"mobs-limit" => 0,
				"gamemode" => 0,
				"force-gamemode" => false,
				"hardcore" => false,
				"pvp" => true,
				"difficulty" => 1,
				"generator-settings" => "",
				"level-name" => "world",
				"level-seed" => "",
				"level-type" => "FLAT",
				"enable-query" => true,
				"enable-rcon" => false,
				"rcon.password" => substr(base64_encode(random_bytes(20)), 3, 10),
				"auto-save" => true,
				"auto-generate" => true,
				"save-player-data" => true,
				"time-update" => true,
				"online-mode" => false
			]);
			
			$dbotcheck = $this->dbot->check();
			$dbotver = $this->getDarkBotVersion();
			
			$version = $this->getFormattedVersion();
			$this->version = $version;
			$mcpe = $this->getVersion();
			$protocol = ProtocolInfo::CURRENT_PROTOCOL;
			$build = ProtocolInfo::DARKSYSTEM_VERSION;
			$tag = \pocketmine\TAG;
			$package = $packages;

			$this->konsol->info("
			
    §e______           _    _____           _                  
    §6|  _  \         | |  /  ___|         | |                  
    §e| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
    §6| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
    §e| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
    §6|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
                                 §e__/  |      
                                 §6|___/            §eMCPE: $mcpe §b($protocol)
                                                      §eDARKBOT: $dbotcheck (v$dbotver)
                                                        
      §aDarkSystem $version ($build)  *$tag*                                                
	
			");
			
			if($dbotcheck = "§aAktif"){
				$this->konsol->info($this->dbot->getStartupMessage());
			}
			
			$this->konsol->info("§aEklentiler Yükleniyor...");
			$nowLang = $this->getProperty("settings.language", "tur");
			if($defaultLang != "Bilinmeyen" && $nowLang != $defaultLang){
				@file_put_contents($configPath, str_replace('language: "' . $nowLang . '"', 'language: "' . $defaultLang . '"', file_get_contents($configPath)));
				$this->config->reload();
				unset($this->propertyCache["settings.language"]);
			}

			$lang = $this->getProperty("settings.language", Language::FALLBACK_LANGUAGE);
			if(file_exists($this->filePath . "src/pocketmine/resources/darksystem_$lang.yml")){
				$content = file_get_contents($file = $this->filePath . "src/pocketmine/resources/darksystem_$lang.yml");
			}else{
				$content = file_get_contents($file = $this->filePath . "src/pocketmine/resources/darksystem_eng.yml");
			}
			
			/*if(file_exists($this->filePath . "src/pocketmine/resources/eklentiler/plugin_required.phar")){
				$contentpl = file_get_contents($filepl = $this->filePath . "src/pocketmine/resources/eklentiler/plugin_required.phar");
			}else{
				$contentpl = file_get_contents($filepl = $this->filePath . "src/pocketmine/resources/eklentiler/plugin_required1.phar");
			}*/
			
			if(!file_exists($this->dataPath . "darksystem.yml")){
				@file_put_contents($this->dataPath . "darksystem.yml", $content);
			}
			
			/*if(!file_exists($this->pluginPath . "plugin_required.phar")){
				@file_put_contents($this->pluginPath . "plugin_required.phar", $contentpl);
			}*/
			
			$internelConfig = new Config($file, Config::YAML, []);
			$this->advancedConfig = new Config($this->dataPath . "darksystem.yml", Config::YAML, []);
			
			$cfgVer = $this->getAdvancedProperty("config.version", 0, $internelConfig);
			$advVer = $this->getAdvancedProperty("config.version", 0);

			$this->loadAdvancedConfig();
			
			$this->forceLanguage = $this->getProperty("settings.force-language", true);
			$this->language = new Language($this->getProperty("settings.language", Language::FALLBACK_LANGUAGE));
			
			if(($poolSize = $this->getProperty("settings.async-workers", "auto")) === "auto"){
				$poolSize = ServerScheduler::$WORKERS;
				$processors = Utils::getCoreCount() - 2;

				if($processors > 0){
					$poolSize = max(1, $processors);
				}
			}

			ServerScheduler::$WORKERS = $poolSize;

			if($this->getProperty("network.batch-threshold", 256) >= 0){
				Network::$BATCH_THRESHOLD = (int) $this->getProperty("network.batch-threshold", 256);
			}else{
				Network::$BATCH_THRESHOLD = -1;
			}
			
			$this->networkCompressionLevel = $this->getProperty("network.compression-level", 6);
			$this->networkCompressionAsync = $this->getProperty("network.async-compression", true);

			$this->autoTickRate = (bool) $this->getProperty("level-settings.auto-tick-rate", true);
			$this->autoTickRateLimit = (int) $this->getProperty("level-settings.auto-tick-rate-limit", 20);
			$this->alwaysTickPlayers = (int) $this->getProperty("level-settings.always-tick-players", false);
			$this->baseTickRate = (int) $this->getProperty("level-settings.base-tick-rate", 1);

			$this->scheduler = new ServerScheduler();
			
			if($this->getConfigBoolean("enable-rcon", false) === true){
				$this->rcon = new RCON($this, $this->getConfigString("rcon.password", ""), $this->getConfigInt("rcon.port", $this->getPort()), ($ip = $this->getIp()) != "" ? $ip : "0.0.0.0", $this->getConfigInt("rcon.threads", 1), $this->getConfigInt("rcon.clients-per-thread", 50));
			}

			$this->entityMetadata = new EntityMetadataStore();
			$this->playerMetadata = new PlayerMetadataStore();
			$this->levelMetadata = new LevelMetadataStore();

			$this->operators = new Config($this->dataPath . "yoneticiler.json", Config::JSON);
			$this->whitelist = new Config($this->dataPath . "beyaz-liste.json", Config::JSON);
			
			if(file_exists($this->dataPath . "engelli.txt") && !file_exists($this->dataPath . "engelli-oyuncular.txt")){
				@rename($this->dataPath . "engelli.txt", $this->dataPath . "engelli-oyuncular.txt");
			}
			
			@touch($this->dataPath . "engelli-oyuncular.txt");
			$this->banByName = new BanList($this->dataPath . "engelli-oyuncular.txt");
			$this->banByName->load();
			@touch($this->dataPath . "engelli-IPler.txt");
			$this->banByIP = new BanList($this->dataPath . "engelli-IPler.txt");
			$this->banByIP->load();
			@touch($this->dataPath . "engelli-CIDler.txt");
			$this->banByCID = new BanList($this->dataPath . "engelli-CIDler.txt");
			$this->banByCID->load();

			$this->maxPlayers = $this->getConfigInt("max-players", 100);
			$this->setAutoSave($this->getConfigBoolean("auto-save", true));
			$this->setAutoGenerate($this->getConfigBoolean("auto-generate", false));
			$this->setSavePlayerData($this->getConfigBoolean("save-player-data", true));
			
			$this->useAnimal = $this->getConfigBoolean("spawn-animals", false);
			$this->animalLimit = $this->getConfigInt("animals-limit", 0);
			$this->useMonster = $this->getConfigBoolean("spawn-mobs", false);
			$this->monsterLimit = $this->getConfigInt("mobs-limit", 0);
			
			if($this->getConfigBoolean("hardcore", false) === true && $this->getDifficulty() < 3){
				$this->setConfigInt("difficulty", 3);
			}
			
			define("pocketmine\\DEBUG", (int) $this->getProperty("debug.level", 1));
			if($this->konsol instanceof MainLogger){
				$this->konsol->setLogDebug(\pocketmine\DEBUG > 1);
			}
			
			define("advanced_cache", $this->getProperty("settings.advanced-cache", true));
			if(\pocketmine\DEBUG >= 0){
				@cli_set_process_title($this->getName() . " " . $this->getDarkSystemVersion());
			}
			
			$this->serverID = Utils::getMachineUniqueId($this->getIp() . $this->getPort());
			
			$this->konsol->debug("Sunucu ID: " . $this->getServerUniqueId());
			$this->konsol->debug("Makine ID: " . Utils::getMachineUniqueId());
			
			$this->network = new Network($this);
			$this->network->setName($this->getMotd());
			
			Timings::init();

			$this->consoleSender = new ConsoleCommandSender();
			$this->cmdMap = new SimpleCommandMap($this);
			
			Entity::init();
			Tile::init();
			InventoryType::init();
			Block::init();
			Enchantment::init();
			Item::init();
			Biome::init();
			TextWrapper::init();
			MetadataConvertor::init();
			
			$this->craftingMgr = new CraftingManager();
			$this->resourceMgr = new ResourcePackManager($this, $this->getDataPath() . "doku_paketleri" . DIRECTORY_SEPARATOR);
			$this->behaviorMgr = new BehaviorPackManager($this, $this->getDataPath() . "behavior_paketleri" . DIRECTORY_SEPARATOR);
			$this->pluginMgr = new PluginManager($this, $this->cmdMap);
			$this->pluginMgr->subscribeToPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE, $this->consoleSender);
			$this->pluginMgr->setUseTimings($this->getProperty("settings.enable-profiling", false));
			$this->profilingTickRate = (float) $this->getProperty("settings.profile-report-trigger", 20);
			$this->pluginMgr->registerInterface(PharPluginLoader::class);
            //$this->pluginMgr->registerInterface(FolderPluginLoader::class);
			$this->pluginMgr->registerInterface(ScriptPluginLoader::class);
			
			register_shutdown_function([$this, "crashReport"]);

			$this->queryRegenerator = new QueryRegenerateEvent($this, 7);
			$this->pluginMgr->loadPlugins($this->pluginPath);
			$this->enablePlugins(PluginLoadOrder::STARTUP);
			$this->network->registerInterface(new RakLibInterface($this));
			
			LevelProviderManager::addProvider($this, Anvil::class);
			//LevelProviderManager::addProvider($this, PMAnvil::class);
			LevelProviderManager::addProvider($this, McRegion::class);
			
			foreach((array) $this->getProperty("worlds", []) as $name => $worldSetting){
			if($this->loadLevel($name) === false){
				$seed = $this->getProperty("worlds.$name.seed", time());
				if(count($options) > 0){
					$options = [
						"preset" => implode(":", $options),
					];
				}else{
					$options = [];
				}

				$this->generateLevel($name, $seed, $options);
			}
		}

		if($this->getDefaultLevel() === null){
			$default = $this->getConfigString("level-name", "world");
			if(trim($default) == ""){
				$this->konsol->warning("level-name cannot be null, using default");
				$default = "world";
				$this->setConfigString("level-name", "world");
			}
			
			if($this->loadLevel($default) === false){
				$seed = $this->getConfigInt("level-seed", time());
				$this->generateLevel($default, $seed === 0 ? time() : $seed);
			}

			$this->setDefaultLevel($this->getLevelByName($default));
		}
		
		$this->properties->save();
		if(!($this->getDefaultLevel() instanceof Level)){
			$this->konsol->emergency("Varsayılan Dünya Yüklenemedi!");
			$this->forceShutdown();
			return;
		}
		
			/*if($this->netherEnabled){
				if(!$this->loadLevel($this->netherName)){
					$this->generateLevel($this->netherName, time(), Generator::getGenerator("nether"));
				}
				
				$this->netherLevel = $this->getLevelByName($this->netherName);
			}
			
			if($this->endEnabled){
                if(!$this->loadLevel($this->endName)){
                    $this->generateLevel($this->endName, time(), Generator::getGenerator("ender"));
                }
                
                $this->endName = $this->getLevelByName($this->endName);
            }*/
            
			if($this->getProperty("ticks-per.autosave", 6000) > 0){
				$this->autoSaveTicks = $this->getProperty("ticks-per.autosave", 6000);
			}

			$this->enablePlugins(PluginLoadOrder::POSTWORLD);
			
			/*if($cfgVer > $advVer){
				$this->konsol->notice("darksystem.yml Dosyası Güncellenmeli!");
				$this->konsol->notice("Şimdiki Sürüm: $advVer | Güncel Sürüm: $cfgVer");
			}*/

			$this->run();
		}catch(\Throwable $e){
			$this->exceptionHandler($e);
		}
	}
	
	public function broadcastMessage($message, $recipients = null){
		if(!is_array($recipients)){
			return $this->broadcast($message, Server::BROADCAST_CHANNEL_USERS);
		}
		
		foreach($recipients as $recipient){
			$recipient->sendMessage($message);
		}

		return count($recipients);
	}
	
	public function broadcastTip($tip, $recipients = null){
		if(!is_array($recipients)){
			$recipients = [];
			foreach($this->pluginMgr->getPermissionSubscriptions(Server::BROADCAST_CHANNEL_USERS) as $permissible){
				if($permissible instanceof Player && $permissible->hasPermission(Server::BROADCAST_CHANNEL_USERS)){
					$recipients[spl_object_hash($permissible)] = $permissible;
				}
			}
		}
		
		foreach($recipients as $recipient){
			$recipient->sendTip($tip);
		}

		return count($recipients);
	}
	
	public function broadcastPopup($popup, $recipients = null){
		if(!is_array($recipients)){
			$recipients = [];
			foreach($this->pluginMgr->getPermissionSubscriptions(Server::BROADCAST_CHANNEL_USERS) as $permissible){
				if($permissible instanceof Player && $permissible->hasPermission(Server::BROADCAST_CHANNEL_USERS)){
					$recipients[spl_object_hash($permissible)] = $permissible;
				}
			}
		}
		
		foreach($recipients as $recipient){
			$recipient->sendPopup($popup);
		}

		return count($recipients);
	}
	
	public function broadcastTitle(string $title, string $subtitle = "", int $fadeIn = -1, int $stay = -1, int $fadeOut = -1, $recipients = null){
		if(!is_array($recipients)){
			$recipients = [];
			foreach($this->pluginMgr->getPermissionSubscriptions(Server::BROADCAST_CHANNEL_USERS) as $permissible){
				if($permissible instanceof Player && $permissible->hasPermission(Server::BROADCAST_CHANNEL_USERS)){
					$recipients[spl_object_hash($permissible)] = $permissible;
				}
			}
		}
		
		foreach($recipients as $recipient){
			$recipient->sendTitle($title, $subtitle, $fadeIn, $stay, $fadeOut);
		}

		return count($recipients);
	}
	
	public function broadcast($message, $permissions){
		$recipients = [];
		foreach(explode(";", $permissions) as $permission){
			foreach($this->pluginMgr->getPermissionSubscriptions($permission) as $permissible){
				if($permissible instanceof CommandSender && $permissible->hasPermission($permission)){
					$recipients[spl_object_hash($permissible)] = $permissible;
				}
			}
		}

		foreach($recipients as $recipient){
			$recipient->sendMessage($message);
		}

		return count($recipients);
	}
	
	public static function broadcastPacket(array $players, DataPacket $packet){
		foreach($players as $p){
			$p->dataPacket($packet);
		}
		
		if(isset($packet->__encapsulatedPacket)){
			unset($packet->__encapsulatedPacket);
		}
	}
	
	public function batchPackets(array $players, array $packets, $forceSync = true){
		$targets = [];
		$neededProtocol = [];
		foreach($players as $p){
			$targets[] = array($p->getIdentifier(), $p->getPlayerProtocol());
			$neededProtocol[$p->getPlayerProtocol()] = $p->getPlayerProtocol();
		}
		
		$newPackets = array();
		foreach($packets as $p){
			foreach($neededProtocol as $protocol){
				if($p instanceof DataPacket){
					if(!$p->isEncoded || count($neededProtocol) > 1){					
						$p->encode($protocol);
					}
					
					$newPackets[$protocol][] = $p->buffer;
				}elseif(count($neededProtocol) == 1){
					$newPackets[$protocol][] = $p;
				}
			}
		}
		
		$data = array();
		$data['packets'] = $newPackets;
		$data['targets'] = $targets;
		$data['networkCompressionLevel'] = $this->networkCompressionLevel;
		$data['isBatch'] = true;
		
		$this->packetMgr->pushMainToThreadPacket(serialize($data));
	}
	
	public function enablePlugins($type){
		foreach($this->pluginMgr->getPlugins() as $pl){
			if(!$pl->isEnabled() && $pl->getDescription()->getOrder() === $type){
				$this->enablePlugin($pl);
			}
		}
		if($type === PluginLoadOrder::POSTWORLD){
			$this->cmdMap->registerServerAliases();
			DefaultPermissions::registerCorePermissions();
		}
	}
	
	public function enablePlugin(Plugin $plugin){
		$this->pluginMgr->enablePlugin($plugin);
	}
	
	public function loadPlugin(Plugin $plugin){
		$this->enablePlugin($plugin);
	}

	public function disablePlugins(){
		$this->pluginMgr->disablePlugins();
	}

	public function checkConsole(){
		if(($line = $this->cmdReader->getLine()) !== null){
			$this->pluginMgr->callEvent($ev = new ServerCommandEvent($this->consoleSender, $line));
			if(!$ev->isCancelled()){
				$this->dispatchCommand($ev->getSender(), $ev->getCommand());
			}
		}
	}
	
	public function dispatchCommand(CommandSender $sender, $commandLine){
		if(!($sender instanceof CommandSender)){
			throw new ServerException("CommandSender Geçerli Değil!");
		}
		if($this->cmdMap->dispatch($sender, $commandLine)){
			return true;
		}
		if($sender instanceof Player){
			$message = $this->getSoftConfig("mesajlar.bilinmeyen-komut", "Sunucumuzda Böyle Bir Komut Yok!");
			if(is_string($message) && strlen($message) > 0){
				$sender->sendMessage(TF::RED . $message);
			}
		}else{
			$sender->sendMessage(TF::RED . "Sunucumuzda Böyle Bir Komut Yok!");
		}
		return false;
	}

	public function reload(){
		foreach($this->levels as $l){
			$l->save();
		}

		$this->pluginMgr->disablePlugins();
		$this->pluginMgr->clearPlugins();
		$this->cmdMap->clearCommands();

		$this->konsol->info("Ayarlar Yeniden Yükleniyor...");
		$this->properties->reload();
		$this->advancedConfig->reload();
		$this->loadAdvancedConfig();
		$this->maxPlayers = $this->getConfigInt("max-players", 100);
		
		$this->banByName->load();
		$this->banByIP->load();
		$this->banByCID->load();
		$this->reloadWhitelist();
		$this->operators->reload();
		
		foreach($this->getIPBans()->getEntries() as $ent){
			$this->blockAddress($ent->getName(), -1);
		}

		$this->pluginMgr->registerInterface(PharPluginLoader::class);
		$this->pluginMgr->loadPlugins($this->pluginPath);
		$this->enablePlugins(PluginLoadOrder::STARTUP);
		$this->enablePlugins(PluginLoadOrder::POSTWORLD);
		TimingsHandler::reload();
	}
	
	public function shutdown($msg = ""){
		$this->isRunning = false;
		if($msg != ""){
			$this->propertyCache["settings.shutdown-message"] = $msg;
		}
	}
	
	public function forceShutdown(){
		if($this->hasStopped){
			return;
		}
		//try{
			$this->hasStopped = true;
			foreach($this->players as $p){
				$p->close(TF::YELLOW . $p->getName() . " Oyundan Ayrıldı", $this->getProperty("settings.shutdown-message", "Sunucu Durduruldu"));
			}
			foreach($this->network->getInterfaces() as $int){
				$int->shutdown();
				$this->network->unregisterInterface($int);
			}
			$this->shutdown();
			//$this->dbot->shutdown();
			$this->konsol->info("§cEklentiler Devre Dışı Bırakılıyor...");
			if($this->rcon instanceof RCON){
				$this->rcon->stop();
			}
			$this->pluginMgr->disablePlugins();
			foreach($this->levels as $l){
				//$l->save();
				$this->unloadLevel($l, true, true);
			}
			HandlerList::unregisterAll();
			$this->scheduler->cancelAllTasks();
			$this->scheduler->mainThreadHeartbeat(PHP_INT_MAX);
			$this->properties->save();
			$this->cmdReader->shutdown();
			$this->cmdReader->notify();
		/*}catch(\Exception $e){
			$this->konsol->emergency("Sunucu Çöktü, Tüm Görevler Durduruluyor!");
			@kill(getmypid());
		}*/
	}
	
	public function getQueryInformation(){
		return $this->queryRegenerator;
	}
	
	public function run(){	
		DataPacket::initializePackets();
		if($this->getConfigBoolean("enable-query", true) === true){
			$this->queryHandler = new QueryHandler();
		}

		foreach($this->getIPBans()->getEntries() as $ent){
			$this->network->blockAddress($ent->getName(), -1);
		}
		
		$this->tickCounter = 0;
		
		Effect::init();

		$this->konsol->info($this->getLanguage()->translateString("pocketmine.server.startFinished", [round(microtime(true) - \pocketmine\START_TIME, 3)]));

		$this->packetMgr = new PacketManager($this->getLoader());
		
		$this->tickAverage = array();
		$this->useAverage = array();
		for($i = 0; $i < 1200; $i++){
			$this->tickAverage[] = 20;
			$this->useAverage[] = 0;
		}

		$this->tickProcessor();
		$this->forceShutdown();

		\gc_collect_cycles();
	}

	public function handleSignal($signo){
		if($signo === SIGTERM || $signo === SIGINT || $signo === SIGHUP){
			$this->shutdown();
		}
	}
	
	public function exceptionHandler(\Throwable $e){
		if($e === null){
			return;
		}

		global $lastError;
		
		$errstr = $e->getMessage();
		$errfile = $e->getFile();
		$errno = $e->getCode();
		$errline = $e->getLine();

		$type = ($errno === E_ERROR || $errno === E_USER_ERROR) ? \LogLevel::ERROR : (($errno === E_USER_WARNING || $errno === E_WARNING) ? \LogLevel::WARNING : \LogLevel::NOTICE);
		if(($pos = strpos($errstr, "\n")) !== false){
			$errstr = substr($errstr, 0, $pos);
		}

		$errfile = cleanPath($errfile);

		if($this->konsol instanceof MainLogger){
			$this->konsol->logException($e);
		}

		$lastError = [
			"type" => $type,
			"message" => $errstr,
			"fullFile" => $e->getFile(),
			"file" => $errfile,
			"line" => $errline
		];

		global $lastExceptionError, $lastError;
		$lastExceptionError = $lastError;
		$this->crashReport();
	}

	public function crashReport(){
		if($this->isRunning === false){
			return;
		}
		
		$this->isRunning = false;
		$this->hasStopped = false;

		ini_set("error_reporting", 0);
		ini_set("memory_limit", -1);
		$this->konsol->emergency($this->getLanguage()->translateString("pocketmine.crash.create"));
		//try{
		$report = new CrashReport($this);
		//}catch(\Exception $e){
			//$this->konsol->critical($this->getLanguage()->translateString("pocketmine.crash.error", $e->getMessage()));
			//return;
		//}

		$this->konsol->emergency($this->getLanguage()->translateString("pocketmine.crash.submit", [$report->getPath()]));
		
		//$this->shutdown();
		$this->forceShutdown();
		$this->isRunning = false;
		@kill(getmypid());
		exit(1);
		exit(1);
		exit(1);
	}
	
	private function tickProcessor(){
		$this->nextTick = microtime(true);
		while($this->isRunning){
			$this->tick();
			$next = $this->nextTick - 0.0001;
			if($next > microtime(true)){
				try{
					@time_sleep_until($next);
				}catch(\Throwable $e){
				}
			}
		}
	}

	public function addOnlinePlayer(Player $player){
		$this->playerList[$player->getRawUniqueId()] = $player;
	}

	public function removeOnlinePlayer(Player $player){
		if(isset($this->playerList[$player->getRawUniqueId()])){
			unset($this->playerList[$player->getRawUniqueId()]);
			$pk = new PlayerListPacket();
			$pk->type = PlayerListPacket::TYPE_REMOVE;
			$pk->entries[] = [$player->getUniqueId()];
			Server::broadcastPacket($this->playerList, $pk);
		}
	}
	
	public function updatePlayerListData(UUID $uuid, $entityId, $name, $skinName, /*$skinId, */$skinData, $skinGeometryName, /*$skinGeometryId, */$skinGeometryData, $capeData, $xuid, array $players = null){
		$pk = new PlayerListPacket();
		$pk->type = PlayerListPacket::TYPE_ADD;
		$pk->entries[] = [$uuid, $entityId, $name, $skinName, /*$skinId, */$skinData, $skinGeometryName, /*$skinGeometryId, */$skinGeometryData, $capeData, $xuid];
		$readyPackets = [];
		foreach($players === null ? $this->playerList : $players as $p){
			$protocol = $p->getPlayerProtocol();
			if(!isset($readyPackets[$protocol])){
				$pk->encode($protocol);
				$batch = new BatchPacket();
				$batch->payload = zlib_encode(Binary::writeVarInt(strlen($pk->getBuffer())) . $pk->getBuffer(), ZLIB_ENCODING_DEFLATE, 7);
				$readyPackets[$protocol] = $batch;
			}
			
			$p->dataPacket($readyPackets[$protocol]);
		}
	}

	public function removePlayerListData(UUID $uuid, array $players = null){
		$pk = new PlayerListPacket();
		$pk->type = PlayerListPacket::TYPE_REMOVE;
		$pk->entries[] = [$uuid];
		foreach($players === null ? $this->playerList : $players as $p){
			$p->dataPacket($pk);
		}
	}
	
	private $craftList = [];
	
	public function sendRecipeList(Player $p){
		if(!isset($this->craftList[$p->getPlayerProtocol()])){
			$pk = new CraftingDataPacket();
			$pk->cleanRecipes = true;
			foreach($this->getCraftingManager()->getRecipes() as $r){
				if($r instanceof ShapedRecipe){
					$pk->addShapedRecipe($r);
				}elseif($r instanceof ShapelessRecipe){
					$pk->addShapelessRecipe($r);
				}
			}

			foreach($this->getCraftingManager()->getFurnaceRecipes() as $r){
				$pk->addFurnaceRecipe($r);
			}
			
			$pk->encode($p->getPlayerProtocol());
			$pk->isEncoded = true;
			$this->craftList[$p->getPlayerProtocol()] = $pk;
		}
		
		$this->batchPackets([$p], [$this->craftList[$p->getPlayerProtocol()]]);
	}

	public function addPlayer($identifier, Player $player){
		$this->players[$identifier] = $player;
		$this->identifiers[spl_object_hash($player)] = $identifier;
	}
	
	public function saveEverything(){
		foreach($this->getOnlinePlayers() as $index => $p){
			if($p->isOnline()){
				$p->save();
			}elseif(!$p->isConnected()){
				$this->removePlayer($p);
			}
		}
			
		foreach($this->levels as $l){
			$l->save(false);
		}
	}
	
	public function doAutoSave(){
		if($this->getAutoSave()){
			$this->saveEverything();
		}
	}

	public function doLevelGC(){
		foreach($this->levels as $l){
			$l->doChunkGarbageCollection();
		}
	}
	
	public function getLanguage(){
		return $this->language;
	}
	
	public function isLanguageForced(){
		return $this->forceLanguage;
	}
	
	public function getNetwork(){
		return $this->network;
	}
	
	public function handlePacket($address, $port, $payload){
		try{
			if(strlen($payload) > 2 && substr($payload, 0, 2) === "\xfe\xfd" && $this->queryHandler instanceof QueryHandler){
				$this->queryHandler->handle($address, $port, $payload);
			}
		}catch(\Exception $e){
			if(\pocketmine\DEBUG > 1){
				if($this->konsol instanceof MainLogger){
					$this->konsol->logException($e);
				}
			}
			$this->getNetwork()->blockAddress($address, 600);
		}
	}
	
	public function getSoftConfig($variable, $defaultValue = null){
		$vars = explode(".", $variable);
		$base = array_shift($vars);
		if($this->softConfig->exists($base)){
			$base = $this->softConfig->get($base);
		}else{
			return $defaultValue;
		}

		while(count($vars) > 0){
			$baseKey = array_shift($vars);
			if(is_array($base) && isset($base[$baseKey])){
				$base = $base[$baseKey];
			}else{
				return $defaultValue;
			}
		}

		return $base;
	}
	
	public function getAdvancedProperty($variable, $defaultValue = null, Config $cfg = null){
		$vars = explode(".", $variable);
		$base = array_shift($vars);
		if($cfg == null) $cfg = $this->advancedConfig;
		if($cfg->exists($base)){
			$base = $cfg->get($base);
		}else{
			return $defaultValue;
		}

		while(count($vars) > 0){
			$baseKey = array_shift($vars);
			if(is_array($base) && isset($base[$baseKey])){
				$base = $base[$baseKey];
			}else{
				return $defaultValue;
			}
		}

		return $base;
	}
	
	private function tick(){
		$tickTime = microtime(true);
		$dbotcheck = $this->dbot->check();
		//if(($tickTime - $this->nextTick) < -0.025){
		if($tickTime < $this->nextTick){
			return false;
		}
		++$this->tickCounter;
		$this->checkConsole();
		/*foreach($this->unloadLevelQueue as $levelForUnload){
			$this->unloadLevel($levelForUnload['level'], $levelForUnload['force'], true);
		}*/
		/*if(($this->tickCounter % 200) === 0){
			foreach($this->levels as $l){
				$l->clearCache();
			}
			$this->saveEverything();
		}*/
		/*if(($this->tickCounter % 1925) === 0){ //LaggClear
			foreach($this->levels as $l){
				foreach($l->getEntities() as $e){
					if($e instanceof Item){
						$e->close();
					}
				}
			}
		}*/
		if($this->autoSave and ++$this->autoSaveTicker >= $this->autoSaveTicks){
			$this->autoSaveTicker = 0;
			$this->doAutoSave();
		}
		$this->unloadLevelQueue = [];
		while(strlen($str = $this->packetMgr->readThreadToMainPacket()) > 0){
			$data = unserialize($str);
			if(isset($this->players[$data['identifier']])){
				$player = $this->players[$data['identifier']];
				$player->getInterface()->putReadyPacket($player, $data['buffer']);
			}
		}
		$this->network->processInterfaces();
		$this->scheduler->mainThreadHeartbeat($this->tickCounter);
		foreach($this->levels as $l){
			$l->doTick($this->tickCounter);
		}
		if(($this->tickCounter & 0b1111) === 0){
			if($this->queryHandler !== null && ($this->tickCounter & 0b111111111) === 0){
				try{
					$this->queryHandler->regenerateInfo();
				}catch(\Exception $e){
					if($this->konsol instanceof MainLogger){
						$this->konsol->logException($e);
					}
				}
			}
		}
		if(($this->tickCounter % 2975) === 0 && $dbotcheck = "§aAktif"){
			switch(mt_rand(1, 5)){
				case 1:
				$this->broadcastMessage($this->getDarkBotPrefix() . "§aSunucu Benimle Güvende!");
				break;
				case 2:
				$this->broadcastMessage($this->getDarkBotPrefix() . "§aBu Sunucu Güvencem Altındadır!");
				break;
				case 3:
				$this->broadcastMessage($this->getDarkBotPrefix() . "§aYakında Oyuna Bende Katılacağım!");
				break;
				case 4:
				$this->broadcastMessage($this->getDarkBotPrefix() . "§aBen Sadece Bir Robot Değilim!");
				break;
				case 5:
				$this->broadcastMessage($this->getDarkBotPrefix() . "§aGüvenlik Önemlidir!");
				break;
				default;
				break;
			}
		}
		/*if(($this->tickCounter % 6375) === 0){
			$this->clearChat();
			$this->broadcastMessage("§b[DarkSystem] §aSohbet Temizlendi!");
		}*/
		/*if(($this->tickCounter % 1275) === 0){ //16025
			$x = $this->getDefaultLevel()->getSafeSpawn()->getX();
			$y = $this->getDefaultLevel()->getSafeSpawn()->getY();
			$z = $this->getDefaultLevel()->getSafeSpawn()->getZ();
			//$skin;
			$eid = "114514";
			$item = Item::get(0);
			$this->dbot->spawn("DarkBot", $eid, $x, $y + 0.1, $z, $skin, $item);
			$this->broadcastPopup("§aDarkBot Oyuna Katıldı!");
		}*/
		$now = microtime(true);
		array_shift($this->tickAverage);
		$tickDiff = $now - $tickTime;
		$this->tickAverage[] = ($tickDiff <= 0.05) ? 20 : 1 / $tickDiff;
		array_shift($this->useAverage);
		$this->useAverage[] = min(1, $tickDiff * 20);
		if(($this->nextTick - $tickTime) < -1){
			$this->nextTick = $tickTime;
		}
		$this->nextTick += 0.05;
		return true;
	}
	
}
