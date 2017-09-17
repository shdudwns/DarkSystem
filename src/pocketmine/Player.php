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

use raklib\Binary;
use darksystem\DSPlayer;
use darksystem\DSPlayerInterface;
use pocketmine\darkbot\DarkBot;
use pocketmine\darkbot\ChatHandler;
use pocketmine\block\Block;
use pocketmine\block\Liquid;
use pocketmine\command\CommandSender;
use pocketmine\entity\Attribute;
use pocketmine\entity\AttributeMap;
use pocketmine\entity\Arrow;
use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Item as DroppedItem;
use pocketmine\entity\Living;
use pocketmine\entity\Projectile;
use pocketmine\entity\OnlinePlayer;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryPickupArrowEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerAnimationEvent;
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\event\player\PlayerBedLeaveEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerCommandPostprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerRespawnAfterEvent;
use pocketmine\event\player\PlayerTalkEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\player\PlayerToggleSprintEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\TextContainer;
use pocketmine\event\Timings;
use pocketmine\inventory\BaseTransaction;
use pocketmine\inventory\BigShapedRecipe;
use pocketmine\inventory\BigShapelessRecipe;
use pocketmine\inventory\CraftingTransactionGroup;
use pocketmine\inventory\EnchantInventory;
use pocketmine\inventory\FurnaceInventory;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\PlayerInventory120;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\inventory\SimpleTransactionGroup;
use pocketmine\inventory\win10\Win10InvLogic;
use pocketmine\item\Elytra;
use pocketmine\item\Item;
use pocketmine\item\Armor;
use pocketmine\item\Tool;
use pocketmine\item\Potion;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\level\format\FullChunk;
use pocketmine\level\format\LevelProvider;
use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\level\sound\LaunchSound;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\metadata\MetadataValue;
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
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\AdventureSettingsPacket;
use pocketmine\network\protocol\AnimatePacket;
use pocketmine\network\protocol\BatchPacket;
use pocketmine\network\protocol\ContainerClosePacket;
use pocketmine\network\protocol\ContainerSetContentPacket;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\DisconnectPacket;
use pocketmine\network\protocol\EntityEventPacket;
use pocketmine\network\protocol\FullChunkDataPacket;
use pocketmine\network\protocol\Info as ProtocolInfo;
use pocketmine\network\protocol\PlayerActionPacket;
use pocketmine\network\protocol\PlayStatusPacket;
use pocketmine\network\protocol\RemoveEntityPacket;
use pocketmine\network\protocol\RespawnPacket;
use pocketmine\network\protocol\SetEntityDataPacket;
use pocketmine\network\protocol\StrangePacket;
use pocketmine\network\protocol\TextPacket;
use pocketmine\network\protocol\MovePlayerPacket;
use pocketmine\network\protocol\SetDifficultyPacket;
use pocketmine\network\protocol\SetEntityMotionPacket;
use pocketmine\network\protocol\SetSpawnPositionPacket;
use pocketmine\network\protocol\SetTimePacket;
use pocketmine\network\protocol\StartGamePacket;
use pocketmine\network\protocol\TakeItemEntityPacket;
use pocketmine\network\protocol\TransferPacket;
use pocketmine\network\protocol\UpdateAttributesPacket;
use pocketmine\network\protocol\SetHealthPacket;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\network\protocol\ChunkRadiusUpdatePacket;
use pocketmine\network\protocol\InteractPacket;
use pocketmine\network\SourceInterface;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\PermissionAttachment;
use pocketmine\plugin\Plugin;
use pocketmine\tile\Sign;
use pocketmine\tile\Spawnable;
use pocketmine\tile\Tile;
use pocketmine\utils\TextFormat as TF;
use pocketmine\scheduler\SendPlayerFaceTask;
use pocketmine\network\protocol\SetPlayerGameTypePacket;
use pocketmine\network\protocol\SetCommandsEnabledPacket;
use pocketmine\network\protocol\AvailableCommandsPacket;
use pocketmine\network\protocol\ResourcePackChunkDataPacket;
use pocketmine\network\protocol\ResourcePackDataInfoPacket;
use pocketmine\network\protocol\ResourcePackInfoPacket;
use pocketmine\network\protocol\ResourcePackStackPacket;
use pocketmine\network\protocol\BehaviorPackChunkDataPacket;
use pocketmine\network\protocol\BehaviorPackDataInfoPacket;
use pocketmine\network\protocol\BehaviorPackInfoPacket;
use pocketmine\network\protocol\BehaviorPackStackPacket;
use pocketmine\network\protocol\SetTitlePacket;
use pocketmine\network\protocol\ResourcePackClientResponsePacket;
use pocketmine\network\protocol\BehaviorPackClientResponsePacket;
use pocketmine\network\protocol\LevelSoundEventPacket;
use pocketmine\network\protocol\LevelEventPacket;
use pocketmine\network\protocol\v120\PlayerSkinPacket;
use pocketmine\network\protocol\v120\ShowModalFormPacket;
use pocketmine\network\protocol\v120\ServerSettingsResponsetPacket;
use pocketmine\network\protocol\v120\InventoryTransactionPacket;
use pocketmine\network\protocol\v120\Protocol120;
use pocketmine\network\multiversion\Multiversion;
use pocketmine\network\multiversion\MultiversionEnums;

//class Player /*extends OnlinePlayer*/ extends Human implements CommandSender, InventoryHolder, IPlayer{
class Player /*extends OnlinePlayer*/ extends Human implements DSPlayerInterface, CommandSender, InventoryHolder, IPlayer{

    const OS_ANDROID = 1;
    const OS_IOS = 2;
    const OS_OSX = 3;
    const OS_FIREOS = 4;
    const OS_GEARVR = 5;
    const OS_HOLOLENS = 6;
    const OS_WIN10 = 7;
    const OS_WIN32 = 8;
    const OS_DEDICATED = 9;
    const OS_ORBIS = 10;
    const OS_NX = 11;
    
    const INVENTORY_CLASSIC = 0;
    const INVENTORY_POCKET = 1;
    
	const SURVIVAL = 0;
	const CREATIVE = 1;
	const ADVENTURE = 2;
	const SPECTATOR = 3;
	const VIEW = Player::SPECTATOR;
	
	const CRAFTING_DEFAULT = 0;
	const CRAFTING_WORKBENCH = 1;
	const CRAFTING_ANVIL = 2;
	const CRAFTING_ENCHANT = 3;

	const SURVIVAL_SLOTS = 42;
	const CREATIVE_SLOTS = 112;
	
	const DEFAULT_SPEED = 0.1;
	const MAXIMUM_SPEED = 0.5;
	
	const MIN_WINDOW_ID = 2;
	
	const MAX_EXPERIENCE = 2147483648;
	const MAX_EXPERIENCE_LEVEL = 21863;
	
	protected $interface;

	public $spawned = false;
	public $loggedIn = false;
	public $dead = false;
	public $gamemode;
	public $lastBreak;
	
	protected $currentWindow = null;
	protected $currentWindowId = -1;
	
	protected $messageCounter = 2;

	protected $sendIndex = 0;

	private $clientSecret;
	
	public $speed = null;

	public $blocked = false;
	public $lastCorrect;
	
	public $craftingType = Player::CRAFTING_DEFAULT;

	protected $isCrafting = false;
	
	public $loginData = [];

	public $creationTime = 0;

	protected $randomClientId;

	protected $lastMovement = 0;
	protected $forceMovement = null;
	
	protected $connected = true;
	protected $ip;
	protected $removeFormat = true;
	protected $port;
	protected $username = '';
	protected $iusername = '';
	protected $displayName = '';
	protected $startAction = -1;
	
	public $protocol = 0;
	
	protected $sleeping = null;
	protected $clientID = null;
	
	private $loaderId = null;
	
	protected $stepHeight = 0.6;

	public $usedChunks = [];
	
	protected $chunkLoadCount = 0;
	protected $loadQueue = [];
	protected $nextChunkOrderRun = 5;
	protected $hiddenPlayers = [];
	protected $hiddenEntity = [];
	
	public $newPosition;

	protected $chunksPerTick;
	protected $spawnThreshold = 16 * M_PI;
	
	private $spawnPosition = null;

	protected $inAirTicks = 0;
	protected $startAirTicks = 15;

	protected $autoJump = true;

	private $checkMovement;
	
	protected $allowFlight = false;
	
	protected $tasks = [];
	
	private $perm = null;
	
	protected $lastMessageReceivedFrom = "";
	
	protected $identifier;
	
	protected static $availableCommands = [];
	
	protected $movementSpeed = Player::DEFAULT_SPEED;
	
	private static $damageTimeList = ['0.1' => 0, '0.15' => 0.4, '0.2' => 0.6, '0.25' => 0.8];
	
	protected $lastDamageTime = 0;
	
	protected $lastTeleportTime = 0;
	
	protected $isTeleportedForMoveEvent = false;
	
	private $isFirstConnect = true;
	
	private $exp = 0;
	private $expLevel = 0;

	private $elytraIsActivated = false;
	
    private $inventoryType = Player::INVENTORY_CLASSIC;
	private $languageCode = false;
	
    private $deviceType = Player::OS_DEDICATED;
	
	private $messageQueue = [];
	
	private $noteSoundQueue = [];
    
    private $xuid = '';
	
	private $ping = 0;
    
    protected $xblName = '';
	
	protected $viewRadius = 4;
	
	private $actionsNum = [];
	
	private $isMayMove = false;
	
	protected $serverAddress = '';
	
	protected $clientVersion = '';
	
	protected $originalProtocol;
	
	protected $lastModalId = 1;
	
	public function getLeaveMessage(){
		return "";
	}
	
	public function getClientId(){
		return $this->randomClientId;
	}

	public function getClientSecret(){
		return $this->clientSecret;
	}

	public function isBanned(){
		return $this->server->getNameBans()->isBanned(strtolower($this->getName()));
	}

	public function setBanned($value){
		if($value === true){
			$this->server->getNameBans()->addBan($this->getName(), null, null, null);
			$this->kick("You have been banned");
		}else{
			$this->server->getNameBans()->remove($this->getName());
		}
	}

	public function isWhitelisted(){
		return $this->server->isWhitelisted(strtolower($this->getName()));
	}

	public function setWhitelisted($value){
		if($value === true){
			$this->server->addWhitelist(strtolower($this->getName()));
		}else{
			$this->server->removeWhitelist(strtolower($this->getName()));
		}
	}

	public function getPlayer(){
		return $this;
	}

	public function getFirstPlayed(){
		return $this->namedtag instanceof Compound ? $this->namedtag["firstPlayed"] : null;
	}

	public function getLastPlayed(){
		return $this->namedtag instanceof Compound ? $this->namedtag["lastPlayed"] : null;
	}

	public function hasPlayedBefore(){
		return $this->namedtag instanceof Compound;
	}

	public function setAllowFlight($value){
		$this->allowFlight = (bool) $value;
		$this->sendSettings();
	}

	public function getAllowFlight(){
		return $this->allowFlight;
	}

	public function setAutoJump($value){
		$this->autoJump = $value;
		$this->sendSettings();
	}

	public function hasAutoJump(){
		return $this->autoJump;
	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player){
		if($this->spawned === true && $player->spawned === true && $this->dead !== true && $player->dead !== true && $player->getLevel() === $this->level && $player->canSee($this) && !$this->isSpectator()){
			parent::spawnTo($player);
		}
	}

	/**
	 * @return Server
	 */
	public function getServer(){
		return $this->server;
	}

	/**
	 * @return bool
	 */
	public function getRemoveFormat(){
		return $this->removeFormat;
	}

	/**
	 * @param bool $remove
	 */
	public function setRemoveFormat($remove = true){
		$this->removeFormat = (bool) $remove;
	}

	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function canSee(Player $player){
		return !isset($this->hiddenPlayers[$player->getName()]);
	}

	/**
	 * @param Player $player
	 */
	public function hidePlayer(Player $player){
		if($player === $this){
			return;
		}
		$this->hiddenPlayers[$player->getName()] = $player;
		$player->despawnFrom($this);
	}

	/**
	 * @param Player $player
	 */
	public function showPlayer(Player $player){
		if($player === $this){
			return;
		}
		unset($this->hiddenPlayers[$player->getName()]);
		if($player->isOnline()){
			$player->spawnTo($this);
		}
	}

	public function canCollideWith(Entity $entity){
		return false;
	}

	public function resetFallDistance(){
		parent::resetFallDistance();
		if($this->inAirTicks !== 0){
			$this->startAirTicks = 15;
		}
		
		$this->inAirTicks = 0;
	}

	/**
	 * @return bool
	 */
	public function isOnline(){
		return $this->connected === true && $this->loggedIn === true;
	}

	/**
	 * @return bool
	 */
	public function isOp(){
		return $this->server->isOp($this->getName());
	}

	/**
	 * @param bool $value
	 */
	public function setOp($value){
		if($value === $this->isOp()){
			return;
		}
		if($value === true){
			$this->server->addOp($this->getName());
		}else{
			$this->server->removeOp($this->getName());
		}
		$this->recalculatePermissions();
	}

	/**
	 * @param permission\Permission|string $name
	 *
	 * @return bool
	 */
	public function isPermissionSet($name){
		return $this->perm->isPermissionSet($name);
	}

	/**
	 * @param permission\Permission|string $name
	 *
	 * @return bool
	 */
	public function hasPermission($name){
		return $this->perm->hasPermission($name);
	}

	/**
	 * @param Plugin $plugin
	 * @param string $name
	 * @param bool   $value
	 */
	public function addAttachment(Plugin $plugin, $name = null, $value = null){
		return $this->perm->addAttachment($plugin, $name, $value);
	}

	/**
	 * @param PermissionAttachment $attachment
	 */
	public function removeAttachment(PermissionAttachment $attachment){
		$this->perm->removeAttachment($attachment);
	}

	public function recalculatePermissions(){
		$this->server->getPluginManager()->unsubscribeFromPermission(Server::BROADCAST_CHANNEL_USERS, $this);
		$this->server->getPluginManager()->unsubscribeFromPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE, $this);

		if($this->perm === null){
			return;
		}

		$this->perm->recalculatePermissions();

		if($this->hasPermission(Server::BROADCAST_CHANNEL_USERS)){
			$this->server->getPluginManager()->subscribeToPermission(Server::BROADCAST_CHANNEL_USERS, $this);
		}
		if($this->hasPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE)){
			$this->server->getPluginManager()->subscribeToPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE, $this);
		}
		
		$this->sendCommandData();
	}

	/**
	 * @return permission\PermissionAttachmentInfo[]
	 */
	public function getEffectivePermissions(){
		return $this->perm->getEffectivePermissions();
	}
	
	public function sendCommandData(){
		$data = new \stdClass();
		$count = 0;
		foreach($this->server->getCommandMap()->getCommands() as $command){
			if($this->hasPermission($command->getPermission()) or $command->getPermission() == null){
			    if(($cmdData = $command->generateCustomCommandData($this)) !== null/* && $command->testPermission($this)*/){
				    ++$count;
				    $data->{$command->getName()}->versions[0] = $cmdData;
				}
			}
		}
		
		/*if(!$command->testPermission($this)){
			$this->sendMessage(TF::RED . "Bu Komutu Kullanmaya İzniniz Yok!");
			return;
		}else{
			$this->sendMessage(TF::RED . "Sunucumuzda Böyle Bir Komut Yok!");
			return;
		}*/
		
		if($count > 0){
			$pk = new AvailableCommandsPacket();
			$pk->commands = json_encode($data);
			$this->dataPacket($pk);
		}
	}
	
	public function __construct(SourceInterface $interface, $clientID, $ip, $port){
		$this->interface = $interface;
		$this->perm = new PermissibleBase($this);
		$this->namedtag = new Compound();
		$this->server = Server::getInstance();
		$this->lastBreak = 0;
		$this->ip = $ip;
		$this->port = $port;
		$this->clientID = $clientID;
		$this->chunksPerTick = (int) $this->server->getProperty("chunk-sending.per-tick", 4);
		$this->spawnPosition = null;
		$this->gamemode = $this->server->getGamemode();
		$this->setLevel($this->server->getDefaultLevel(), true);
		$this->newPosition = new Vector3(0, 0, 0);
		$this->checkMovement = (bool) $this->server->getAdvancedProperty("main.check-movement", true);
		$this->boundingBox = new AxisAlignedBB(0, 0, 0, 0, 0, 0);

		$this->uuid = null;
		$this->rawUUID = null;

		$this->creationTime = microtime(true);
		
		$this->inventory = new PlayerInventory($this);
	}
	
	public function setViewRadius($radius){
		$this->viewRadius = $radius;
	}

	/**
	 * @return bool
	 */
	public function isConnected(){
		return $this->connected === true;
	}

	/**
	 * @return string
	 */
	public function getDisplayName(){
		return $this->displayName;
	}

	/**
	 * @param string $name
	 */
	public function setDisplayName($name){
		$this->displayName = $name;
	}

	/**
	 * @return string
	 */
	public function getNameTag(){
		return $this->nameTag;
	}
	
	public function setSkin($str, /*$skinId, */$skinName, $skinGeometryName = "", /*$skinGeometryId = "", */$skinGeometryData = "", $capeData = ""){
		parent::setSkin($str, /*$skinId, */$skinName, $skinGeometryName, /*$skinGeometryId, */$skinGeometryData, $capeData);
		if($this->spawned === true){
			$this->server->updatePlayerListData($this->getUniqueId(), $this->getId(), $this->getName(), $this->skinName, /*$this->skinId, */$this->skin, $this->skinGeometryName, /*$this->skinGeometryId, */$this->skinGeometryData, $this->capeData, $this->getXUID(), $this->getViewers());
		}
	}
	
	/**
	 * @return string
	 */
	public function getAddress(){
		return $this->ip;
	}

	/**
	 * @return int
	 */
	public function getPort(){
		return $this->port;
	}

	/**
	 * @return bool
	 */
	public function isSleeping(){
		return $this->sleeping !== null;
	}

	public function unloadChunk($x, $z){
		$index = Level::chunkHash($x, $z);
		if(isset($this->usedChunks[$index])){
			foreach($this->level->getChunkEntities($x, $z) as $entity){
				if($entity !== $this){
					$entity->despawnFrom($this);
				}
			}
			unset($this->usedChunks[$index]);
		}
		$this->level->freeChunk($x, $z, $this);
		unset($this->loadQueue[$index]);
	}

	/**
	 * @return Position
	 */
	public function getSpawn(){
		if($this->spawnPosition instanceof Position && $this->spawnPosition->getLevel() instanceof Level){
			return $this->spawnPosition;
		}else{
			$level = $this->server->getDefaultLevel();
			return $level->getSafeSpawn();
		}
	}

	public function sendChunk($x, $z, $payload){ //$data
		if($this->connected === false){
			return;
		}
		$data = $payload[$this->getPlayerProtocol()];
		$this->usedChunks[Level::chunkHash($x, $z)] = true;
		$this->chunkLoadCount++;
		$pk = new BatchPacket();
		$pk->payload = $data;
		$this->dataPacket($pk);
		$this->server->getDefaultLevel()->useChunk($x, $z, $this);
		if($this->spawned){
			foreach($this->level->getChunkEntities($x, $z) as $entity){
				if($entity !== $this && !$entity->closed && !$entity->dead && $this->canSeeEntity($entity)){
					$entity->spawnTo($this);
				}
			}
		}
	}

	protected function sendNextChunk(){
		if($this->connected === false){
			return;
		}
		$count = 0;
		foreach($this->loadQueue as $index => $distance){
			if($count >= $this->chunksPerTick){
				break;
			}
			$X = null;
			$Z = null;
			Level::getXZ($index, $X, $Z);
			++$count;
			unset($this->loadQueue[$index]);
			$this->usedChunks[$index] = false;
			$this->level->useChunk($X, $Z, $this);
			$this->level->requestChunk($X, $Z, $this);
			//$this->level->requestChunk($X, $Z, $this, LevelProvider::ORDER_ZXY);
			if($this->server->getAutoGenerate()){
				if(!$this->level->populateChunk($X, $Z, true)){
					if($this->spawned){
						continue;
					}else{
						break;
					}
				}
			}
		}
		if((!$this->isFirstConnect || $this->chunkLoadCount >= $this->spawnThreshold) && $this->spawned === false){
			$this->server->getPluginManager()->callEvent($ev = new PlayerLoginEvent($this, "Eklenti Hatası"));
			if($ev->isCancelled()){
				$this->close(TF::YELLOW . $this->username . " has left the game", $ev->getKickMessage());
				return;
			}
			$this->spawned = true;
			$this->sendSettings();
			$this->sendPotionEffects($this);
			$this->sendData($this);
			$this->inventory->sendContents($this);
			$this->inventory->sendArmorContents($this);
			$this->inventory->setHeldItemIndex(0);
			$pk = new SetTimePacket();
			$pk->time = $this->level->getTime();
			$pk->started = $this->level->stopTime == false;
			$this->dataPacket($pk);
			$pk = new PlayStatusPacket();
			$pk->status = PlayStatusPacket::PLAYER_SPAWN;
			$this->dataPacket($pk);
			$this->server->updatePlayerListData($this->getUniqueId(), $this->getId(), $this->getName(), $this->skinName, /*$this->skinId, */$this->skin, $this->skinGeometryName, /*$this->skinGeometryId, */$this->skinGeometryData, $this->capeData, $this->getXUID(), [$this]);
			$pos = $this->level->getSafeSpawn($this);
			$this->server->getPluginManager()->callEvent($ev = new PlayerRespawnEvent($this, $pos));
			$pos = $ev->getRespawnPosition();
			$this->noDamageTicks = 60;
			$chunkX = $chunkZ = null;
			foreach($this->usedChunks as $index => $c){
				Level::getXZ($index, $chunkX, $chunkZ);
				foreach($this->level->getChunkEntities($chunkX, $chunkZ) as $entity){
					if($entity !== $this && !$entity->closed && !$entity->dead && $this->canSeeEntity($entity)){
						$entity->spawnTo($this);
					}
				}
			}
			$this->teleport($pos);
			$this->server->getPluginManager()->callEvent($ev = new PlayerJoinEvent($this, ""));
			//if(!$ev->isCancelled()){
				foreach($this->server->getOnlinePlayers() as $p){
					$p->sendMessage(TF::GREEN . $this->username . " Oyuna Katıldı!");
				//}
			}
		}
	}

	protected function orderChunks(){
		if($this->connected === false){
			return false;
		}
		$this->nextChunkOrderRun = 200;
		$radiusSquared = $this->viewRadius ** 2;
		$centerX = $this->x >> 4;
		$centerZ = $this->z >> 4;
		$newOrder = [];
		$lastChunk = $this->usedChunks;
		for($dx = 0; $dx < $this->viewRadius; $dx++){
			for($dz = 0; $dz < $this->viewRadius; $dz++){
				if($dx ** 2 + $dz ** 2 > $radiusSquared){
					continue;
				}
				foreach([$dx, (-$dx - 1)] as $ddx){
					foreach([$dz, (-$dz - 1)] as $ddz){
						$chunkX = $centerX + $ddx;
						$chunkZ = $centerZ + $ddz;
						$index = Level::chunkHash($chunkX, $chunkZ);
						if(isset($lastChunk[$index])){
							unset($lastChunk[$index]);
						}else{
							$newOrder[$index] = abs($dx) + abs($dz);
						}
					}
				}
			}
		}
		foreach($lastChunk as $index => $Yndex){
			$X = null;
			$Z = null;
			Level::getXZ($index, $X, $Z);
			$this->unloadChunk($X, $Z);
		}
		$this->loadQueue = $newOrder;
		return true;
	}

	/**
	 * @param DataPacket $packet
	 * @param bool       $needACK
	 *
	 * @return int|bool
	 */
	public function dataPacket(DataPacket $packet, $needACK = false){
		if($this->connected === false){
			return false;
		}
		
		$disallowedPackets = [];
		$protocol = $this->getPlayerProtocol();
		if($protocol >= ProtocolInfo::PROTOCOL_120){
			$disallowedPackets = Protocol120::getDisallowedPackets();
		}
		
		if(in_array(get_class($packet), $disallowedPackets)){
			return;
		}
		
		$this->server->getPluginManager()->callEvent($ev = new DataPacketSendEvent($this, $packet));
		if($ev->isCancelled()){
			return true;
		}
		
		$this->interface->putPacket($this, $packet, $needACK, false);	
		return true;
	}

	/**
	 * @param DataPacket $packet
	 * @param bool       $needACK
	 *
	 * @return bool|int
	 */
	public function directDataPacket(DataPacket $packet, $needACK = false){
		if($this->connected === false){
			return false;
		}
		$this->server->getPluginManager()->callEvent($ev = new DataPacketSendEvent($this, $packet));
		if($ev->isCancelled()){
			return true;
		}

		$this->interface->putPacket($this, $packet, $needACK, true);
		return true;
	}

	/**
	 * @param Vector3 $pos
	 *
	 * @return boolean
	 */
	public function sleepOn(Vector3 $pos){
		foreach($this->level->getNearbyEntities($this->boundingBox->grow(2, 1, 2), $this) as $p){
			if($p instanceof Player){
				if($p->sleeping !== null && $pos->distance($p->sleeping) <= 0.1){
					return false;
				}
			}
		}

		$this->server->getPluginManager()->callEvent($ev = new PlayerBedEnterEvent($this, $this->level->getBlock($pos)));
		if($ev->isCancelled()){
			return true;
		}

		$this->sleeping = clone $pos;
		$this->teleport(new Position($pos->x + 0.5, $pos->y - 0.5, $pos->z + 0.5, $this->level));
			
		$this->setDataProperty(Player::DATA_PLAYER_BED_POSITION, Player::DATA_TYPE_POS, [$pos->x, $pos->y, $pos->z]);
		$this->setDataFlag(Player::DATA_PLAYER_FLAGS, Player::DATA_PLAYER_FLAG_SLEEP, true);

		$this->setSpawn($pos);
		return true;
	}
	
	public function setSpawn(Vector3 $pos){
		if(!($pos instanceof Position)){
			$level = $this->level;
		}else{
			$level = $pos->getLevel();
		}
		
		$this->spawnPosition = new Position($pos->x, $pos->y, $pos->z, $level);
		$pk = new SetSpawnPositionPacket();
		$pk->x = (int) $this->spawnPosition->x;
		$pk->y = (int) $this->spawnPosition->y + 0.1;
		$pk->z = (int) $this->spawnPosition->z;
		$this->dataPacket($pk);
	}

	public function stopSleep(){
		if($this->sleeping instanceof Vector3){
			$this->server->getPluginManager()->callEvent($ev = new PlayerBedLeaveEvent($this, $this->level->getBlock($this->sleeping)));

			$this->sleeping = null;
			$this->setDataFlag(Player::DATA_PLAYER_FLAGS, Player::DATA_PLAYER_FLAG_SLEEP, false);
			$this->setDataProperty(Player::DATA_PLAYER_BED_POSITION, Player::DATA_TYPE_POS, [0, 0, 0]);

			$this->level->sleepTicks = 0;

			$pk = new AnimatePacket();
			$pk->eid = $this->id;
			$pk->action = 3;
			$this->dataPacket($pk);
		}
	}
	
	public function checkSleep(){
		if($this->sleeping instanceof Vector3){
			$time = $this->level->getTime() % Level::TIME_FULL;
			if($time >= Level::TIME_NIGHT && $time < Level::TIME_SUNRISE){
				foreach($this->level->getPlayers() as $p){
					if($p->sleeping === null){
						return;
					}
				}

				$this->level->setTime($this->level->getTime() + Level::TIME_FULL - $time);
				foreach($this->level->getPlayers() as $p){
					$p->stopSleep();
				}
			}
		}
	}

	/**
	 * @return int
	 */
	public function getGamemode(){
		return $this->gamemode;
	}

	/**
	 * @param int $gm
	 *
	 * @return bool
	 */
	public function setGamemode($gm){
		if($gm < 0 || $gm > 3 || $this->gamemode === $gm){
			return false;
		}
		$this->server->getPluginManager()->callEvent($ev = new PlayerGameModeChangeEvent($this, (int) $gm));
		if($ev->isCancelled()){
			return true;
		}
		$this->gamemode = $gm;
		$this->allowFlight = $this->isCreative();
		if($this->isSpectator()){
			$this->despawnFromAll();
		}
		$this->namedtag->playerGameType = new IntTag("playerGameType", $this->gamemode);
		$pk = new SetPlayerGameTypePacket();
		$pk->gamemode = $this->gamemode & 0x01;
		$this->dataPacket($pk);
		$this->sendSettings();
		$this->inventory->sendContents($this);
		$this->inventory->sendContents($this->getViewers());
		$this->inventory->sendHeldItem($this->hasSpawned);
		$this->inventory->setHeldItemIndex(0);
		return true;
	}
	
	public function sendSettings(){
		$flags = 0;
		if($this->isAdventure()){
			$flags |= 0x01;
		}
		if($this->autoJump){
			$flags |= 0x20;
		}
		if($this->allowFlight){
			$flags |= 0x40;
		}
		if($this->isSpectator()){
			$flags |= 0x80;
		}
		$flags |= 0x02;
		$flags |= 0x04;
		$pk = new AdventureSettingsPacket();
		$pk->flags = $flags;
		$this->dataPacket($pk);
	}

	public function isSurvival(){
		return ($this->gamemode & 0x01) === 0;
	}

	public function isCreative(){
		return ($this->gamemode & 0x01) > 0;
	}

	public function isSpectator(){
		return $this->gamemode === 3;
	}

	public function isAdventure(){
		return ($this->gamemode & 0x02) > 0;
	}

	public function getDrops(){
		if(!$this->isCreative() || !$this->isSpectator()){
			return parent::getDrops();
		}
		return [];
	}
	
	public function addEntityMotion($entityId, $x, $y, $z){

	}
	
	public function addEntityMovement($entityId, $x, $y, $z, $yaw, $pitch, $headYaw = null){

	}
	
	protected function checkGroundState($movX, $movY, $movZ, $dx, $dy, $dz){
		
	}

	protected function checkBlockCollision(){

	}

	protected function checkNearEntities($tickDiff){
		foreach($this->level->getNearbyEntities($this->boundingBox->grow(1, 0.5, 1), $this) as $entity){
			$entity->scheduleUpdate();
			if(!$entity->isAlive()){
				continue;
			}

			if($entity instanceof Arrow && $entity->hadCollision){
				$item = Item::get(Item::ARROW, 0, 1);
				//if($this->isSurvival() || $this->isAdventure() || $this->isCreative() && !$this->isSpectator() && !$this->inventory->canAddItem($item)){
				if($this->isSurvival() and !$this->inventory->canAddItem($item)){
					continue;
				}

				$this->server->getPluginManager()->callEvent($ev = new InventoryPickupArrowEvent($this->inventory, $entity));
				if($ev->isCancelled()){
					continue;
				}

				$pk = new TakeItemEntityPacket();
				$pk->eid = $this->getId();
				$pk->target = $entity->getId();
				Server::broadcastPacket($entity->getViewers(), $pk);
				
				$this->inventory->addItem(clone $item);
				$entity->kill();
			}elseif($entity instanceof DroppedItem){
				if($entity->getPickupDelay() <= 0){
					$item = $entity->getItem();
					if($item instanceof Item){
						//if($this->isSurvival() || $this->isAdventure() || $this->isCreative() && !$this->isSpectator() && !$this->inventory->canAddItem($item)){
						if($this->isSurvival() && !$this->inventory->canAddItem($item)){
							continue;
						}

						$this->server->getPluginManager()->callEvent($ev = new InventoryPickupItemEvent($this->inventory, $entity));
						if($ev->isCancelled()){
							continue;
						}
						
						$pk = new TakeItemEntityPacket();
						$pk->eid = $this->getId();
						$pk->target = $entity->getId();
						Server::broadcastPacket($entity->getViewers(), $pk);
						
						$this->inventory->addItem(clone $item);
						$entity->kill();
						
						if($this->inventoryType == Player::INVENTORY_CLASSIC && $this->protocol < ProtocolInfo::PROTOCOL_120){
							Win10InvLogic::playerPickUpItem($this, $item);
						}
					}
				}
			}
		}
	}
	
	protected function revertMovement(Vector3 $pos, $yaw = 0, $pitch = 0){
		/*$this->sendPosition($pos, $yaw, $pitch, MovePlayerPacket::MODE_RESET);
		$this->forceMovement = $pos;
		$this->newPosition = null;*/
	}
	
	protected function processMovement($tickDiff){
		if(!$this->isAlive() || !$this->spawned || $this->newPosition === null){
			$this->setMoving(false);
			return;
		}
		$distanceSquared = ($this->newPosition->x - $this->x) ** 2 + ($this->newPosition->z - $this->z) ** 2;
		if(($distanceSquared / ($tickDiff ** 2)) > $this->movementSpeed * 230){
			//$this->revertMovement($this, $this->lastYaw, $this->lastPitch);
			//return;
		}
		$newPos = $this->newPosition;
		if($this->chunk === null || !$this->chunk->isGenerated()){
			$chunk = $this->level->getChunk($newPos->x >> 4, $newPos->z >> 4);
			if($chunk === null || !$chunk->isGenerated()){
				//$this->revertMovement($this, $this->lastYaw, $this->lastPitch);
				//$this->nextChunkOrderRun = 0;
				//$return;
			}
		}
		$from = new Location($this->x, $this->y, $this->z, $this->lastYaw, $this->lastPitch, $this->level);
		$to = new Location($newPos->x, $newPos->y, $newPos->z, $this->yaw, $this->pitch, $this->level);
		$deltaAngle = abs($from->yaw - $to->yaw) + abs($from->pitch - $to->pitch);
		$distanceSquared += ($this->newPosition->y - $this->y) ** 2;
		if($distanceSquared > 0.0625 || $deltaAngle > 10){
			$isFirst = ($this->lastX === null || $this->lastY === null || $this->lastZ === null);
			if(!$isFirst){
				if(!$this->isSpectator()){
					$toX = floor($to->x);
					$toZ = floor($to->z);
					$toY = ceil($to->y);
					$block = $from->level->getBlock(new Vector3($toX, $toY, $toZ));
					$blockUp = $from->level->getBlock(new Vector3($toX, $toY + 1, $toZ));
					$roundBlock = $from->level->getBlock(new Vector3($toX, round($to->y), $toZ));
					if($from->y - $to->y > 0.1){
						if(!$roundBlock->isTransparent()){
							//$this->revertMovement($this, $this->lastYaw, $this->lastPitch);
							//return;
						}
					}else{
						if(!$block->isTransparent() || !$blockUp->isTransparent()){
							$blockUpUp = $from->level->getBlock(new Vector3($toX, $toY + 2, $toZ));
							if(!$blockUp->isTransparent()){
								$blockLow = $from->level->getBlock(new Vector3($toX, $toY - 1, $toZ));
								if($from->y == $to->y && !$blockLow->isTransparent()){
									//$this->revertMovement($this, $this->lastYaw, $this->lastPitch);
									//return;
								}
							}else{
								if(!$blockUpUp->isTransparent()){
									//$this->revertMovement($this, $this->lastYaw, $this->lastPitch);
									//return;
								}
								$blockFrom = $from->level->getBlock(new Vector3($from->x, $from->y, $from->z));
								if($blockFrom instanceof Liquid){
									//$this->revertMovement($this, $this->lastYaw, $this->lastPitch);
									//return;
								}
							}
						}
					}
				}
				$this->isTeleportedForMoveEvent = false;
				$ev = new PlayerMoveEvent($this, $from, $to);
				$this->setMoving(true);
				$this->server->getPluginManager()->callEvent($ev);
				if($this->isTeleportedForMoveEvent){
					return;
				}
				if($ev->isCancelled()){
					//$this->revertMovement($this, $this->lastYaw, $this->lastPitch);
					//return;
				}
				if($to->distanceSquared($ev->getTo()) > 0.01){
					//$this->teleport($ev->getTo());
					//return;
				}
			}
			$dx = $to->x - $from->x;
			$dy = $to->y - $from->y;
			$dz = $to->z - $from->z;
			$this->move($dx, $dy, $dz);
			$this->x = $to->x;
			$this->y = $to->y;
			$this->z = $to->z;
			$this->lastX = $to->x;
			$this->lastY = $to->y;
			$this->lastZ = $to->z;
			$this->lastYaw = $to->yaw;
			$this->lastPitch = $to->pitch;
			$this->level->addEntityMovement($this->getViewers(), $this->getId(), $this->x, $this->y + $this->getVisibleEyeHeight(), $this->z, $this->yaw, $this->pitch, $this->yaw, true);
			if(!$this->isSpectator()){
				$this->checkNearEntities($tickDiff);
			}
			if($distanceSquared == 0){
				$this->speed = new Vector3(0, 0, 0);
				$this->setMoving(false);
			}else{
				$this->speed = $from->subtract($to);
				if($this->nextChunkOrderRun > 20){
					$this->nextChunkOrderRun = 20;
				}
			}
			$this->forceMovement = null;
		}
		$this->newPosition = null;
	}

	protected $foodTick = 0;

	protected $starvationTick = 0;

	protected $foodUsageTime = 0;

	protected $moving = false;

	public function setMoving($moving){
		$this->moving = $moving;
	}

	public function isMoving(){
		return $this->moving;
	}

	public function setMotion(Vector3 $mot){
		if(parent::setMotion($mot)){
			if($this->chunk !== null){
				$this->level->addEntityMotion($this->getViewers(), $this->getId(), $this->motionX, $this->motionY, $this->motionZ);
				$pk = new SetEntityMotionPacket();
				$pk->entities[] = [$this->id, $mot->x, $mot->y, $mot->z];
				$this->dataPacket($pk);
			}

			if($this->motionY > 999){ //Disable
				//$this->startAirTicks = (-(log($this->gravity / ($this->gravity + $this->drag * $this->motionY))) / $this->drag) * 2 + 5;
			}

			return true;
		}
		
		return false;
	}
	
	public function sendAttributes(bool $sendAll = false){
		$entries = $sendAll ? $this->attributeMap->getAll() : $this->attributeMap->needSend();
		if(count($entries) > 0){
			$pk = new UpdateAttributesPacket();
			$pk->entityId = $this->id;
			$pk->entries = $entries;
			$this->dataPacket($pk);
			foreach($entries as $entry){
				$entry->markSynchronized();
			}
		}
	}
	
	public function onUpdate($currentTick){
		if(!$this->loggedIn){
			return false;
		}
		$tickDiff = $currentTick - $this->lastUpdate;
		if($tickDiff <= 0){
			return true;
		}
		$this->messageCounter = 2;
		$this->lastUpdate = $currentTick;
		if($this->nextChunkOrderRun-- <= 0 || $this->chunk === null){
			$this->orderChunks();
		}
		if(count($this->loadQueue) > 0 || !$this->spawned){
			$this->sendNextChunk();
		}
		if($this->dead === true && $this->spawned){
			++$this->deadTicks;
			if($this->deadTicks >= 10){
				$this->despawnFromAll();
			}
			return $this->deadTicks < 10;
		}
		if($this->spawned){
			$this->processMovement($tickDiff);
			$this->entityBaseTick($tickDiff);
			if(!$this->isSpectator() && $this->speed !== null){
				if($this->onGround || $this->isCollideWithLiquid()){
					if($this->inAirTicks !== 0){
						//$this->startAirTicks = 15;
					}
					$this->inAirTicks = 0;
					if($this->elytraIsActivated){
						$this->setFlyingFlag(false);
						$this->elytraIsActivated = false;
					}
				}else{
					if(!$this->isUseElytra() && !$this->allowFlight && !$this->isSleeping()){
						$expectedVelocity = (-$this->gravity) / $this->drag - ((-$this->gravity) / $this->drag) * exp(-$this->drag * ($this->inAirTicks - $this->startAirTicks));
						$diff = ($this->speed->y - $expectedVelocity) ** 2;
						if(!$this->hasEffect(Effect::JUMP) && $diff > 0.6 && $expectedVelocity < $this->speed->y && !$this->server->getAllowFlight() && !$this->isOp()){
							if($this->inAirTicks < 312){
							}elseif($this->kick("Uçmak Yasak!")){
								return false;
							}
						}
						++$this->inAirTicks;
					}	
				}
			}
			if($this->starvationTick >= 20 && !strpos($this->level->getFolderName(), "RPG")){ //For something
				$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_CUSTOM, 1);
				$this->attack(1, $ev);
				$this->starvationTick = 0;
			}
			if($this->getFood() <= 0){
				$this->starvationTick++;
			}
			if($this->isMoving() && $this->isSurvival() || $this->isAdventure() && !$this->isCreative() && !$this->isSpectator()){
				if($this->isSprinting()){
					$this->foodUsageTime += 500;
				}else{
					$this->foodUsageTime += 250;
				}
			}
			if($this->foodUsageTime >= 100000 && $this->hungerDepletion){
				$this->foodUsageTime -= 100000;
				$this->subtractFood(1);
			}
			if($this->foodTick >= 80){
				if($this->getHealth() < $this->getMaxHealth() && $this->getFood() >= 18){
					$ev = new EntityRegainHealthEvent($this, 1, EntityRegainHealthEvent::CAUSE_EATING);
					$this->heal(1, $ev);
					if(!$ev->isCancelled()){
						if($this->hungerDepletion >=2){
							$this->subtractFood(1);
							$this->foodDepletion = 0;
						}else{
							$this->hungerDepletion++;
						}
					}else{
						$pk = new UpdateAttributesPacket();
						$pk->entityId = $this->id;
						$pk->minValue = 0;
						$pk->maxValue = $this->getMaxHealth();
						$pk->value = $this->getHealth();
						$pk->defaultValue = $pk->maxValue;
						$pk->name = UpdateAttributesPacket::HEALTH;
						$this->dataPacket($pk);
					}
				}
				$this->foodTick = 0;
			}
			if($this->getHealth() < $this->getMaxHealth()){
				$this->foodTick++;
			}
			$this->checkChunks();
		}
		if(count($this->messageQueue) > 0){
			$message = array_shift($this->messageQueue);
			$pk = new TextPacket();
			$pk->type = TextPacket::TYPE_RAW;
			$pk->message = $message;
			$this->dataPacket($pk);
		}
		if(count($this->noteSoundQueue) > 0){
			$noteId = array_shift($this->noteSoundQueue);
			$this->sendNoteSound($noteId);
		}
		return true;
	}

	public function eatFoodInHand(){
		if(!$this->spawned){
			return;
		}

		$items = [
			Item::APPLE => 4,
			Item::MUSHROOM_STEW => 6,
			Item::BEETROOT_SOUP => 5,
			Item::BREAD => 5,
			Item::RAW_PORKCHOP => 2,
			Item::COOKED_PORKCHOP => 8,
			Item::RAW_BEEF => 3,
			Item::STEAK => 8,
			Item::COOKED_CHICKEN => 6,
			Item::RAW_CHICKEN => 2,
			Item::MELON_SLICE => 2,
			Item::GOLDEN_APPLE => 4,
			Item::PUMPKIN_PIE => 8,
			Item::CARROT => 3,
			Item::POTATO => 1,
			Item::BAKED_POTATO => 5,
			Item::COOKIE => 2,
			Item::COOKED_FISH => [
				0 => 5,
				1 => 6
			],
			Item::RAW_FISH => [
				0 => 2,
				1 => 2,
				2 => 1,
				3 => 1
			],
            Item::CHORUS_FRUIT => 2,
		];

		$slot = $this->inventory->getItemInHand();
		$slotId = $slot->getId();
		if(isset($items[$slotId])){
			if($this->getFood() < 20 && $this->getFood() >= -1){
				$this->server->getPluginManager()->callEvent($ev = new PlayerItemConsumeEvent($this, $slot));
				if($ev->isCancelled()){
					$this->inventory->sendContents($this);
					return;
				}

				$pk = new EntityEventPacket();
				$pk->eid = $this->getId();
				$pk->event = EntityEventPacket::USE_ITEM;
				$this->dataPacket($pk);
				Server::broadcastPacket($this->getViewers(), $pk);

				$amount = $items[$slotId];
				if($amount > 20){
					$amount - $amount / 2;
				}
				
				if(is_array($amount)){
					$amount = isset($amount[$slot->getDamage()]) ? $amount[$slot->getDamage()] : 0;
				}
				
				$this->setFood($this->getFood() + $amount);

				--$slot->count;
				$this->inventory->setItemInHand($slot);
				switch($slotId){
					case Item::MUSHROOM_STEW:
					case Item::BEETROOT_SOUP:
						$this->inventory->addItem(Item::get(Item::BOWL, 0, 1));
						break;
					case Item::RAW_FISH:
						if($slot->getDamage() === 3){
							$this->addEffect(Effect::getEffect(Effect::HUNGER)->setAmplifier(2)->setDuration(15 * 20));
							//$this->addEffect(Effect::getEffect(Effect::NAUSEA)->setAmplifier(1)->setDuration(15 * 20));
							$this->addEffect(Effect::getEffect(Effect::POISON)->setAmplifier(3)->setDuration(60 * 20));
						}
						break;
					case Item::GOLDEN_APPLE:
						$this->addEffect(Effect::getEffect(Effect::REGENERATION)->setAmplifier(1)->setDuration(5 * 20));
						//$this->addEffect(Effect::getEffect(Effect::ABSORPTION)->setAmplifier(0)->setDuration(120 * 20));
						break;
					case Item::ENCHANTED_GOLDEN_APPLE:
						$this->addEffect(Effect::getEffect(Effect::REGENERATION)->setAmplifier(4)->setDuration(30 * 20));
						//$this->addEffect(Effect::getEffect(Effect::ABSORPTION)->setAmplifier(0)->setDuration(120 * 20));
						$this->addEffect(Effect::getEffect(Effect::DAMAGE_RESISTANCE)->setAmplifier(0)->setDuration(300 * 20));
						$this->addEffect(Effect::getEffect(Effect::FIRE_RESISTANCE)->setAmplifier(0)->setDuration(300 * 20));
						break;
				}
			}
		}
	}

	/**
	 * @param DataPacket $packet
	 */
	public function handleDataPacket(DataPacket $packet){
		if($this->connected === false){
			return;
		}
		if($packet->pname() === 'DOS_PACKET' || $packet->pname() === 'DDOS_PACKET' || $packet->pname() === 'HACK_PACKET'){
			return;
		}
		if($packet->pname() === 'BATCH_PACKET'){
			$this->server->getNetwork()->processBatch($packet, $this);
			return;
		}
		$beforeLoginAvailablePackets = ['LOGIN_PACKET', 'REQUEST_CHUNK_RADIUS_PACKET', 'RESOURCE_PACK_CLIENT_RESPONSE_PACKET'];
		if(!$this->isOnline() && !in_array($packet->pname(), $beforeLoginAvailablePackets)){
			return;
		}
		switch($packet->pname()){
            case 'SET_PLAYER_GAMETYPE_PACKET':
                break;
            case 'UPDATE_ATTRIBUTES_PACKET':
                break;
            case 'ADVENTURE_SETTINGS_PACKET':
                $isHacker = ($this->allowFlight === false && ($packet->flags >> 9) & 0x01 === 1) || 
                    (!$this->isSpectator() && ($packet->flags >> 7) & 0x01 === 1);
                if($isHacker){
                	$this->kick("Lütfen Hile Kullanmayınız!");
                }
                break;
			case 'LOGIN_PACKET':
				if($this->loggedIn === true){
					break;
				}
				if($packet->isValidProtocol === false){
					$this->protocol = ProtocolInfo::BASE_PROTOCOL;
					$this->close("", TF::RED . "Oyun Sürümünüz Uyumlu Değil!");
					break;
				}
				$this->username = TF::clean($packet->username);
                $this->xblName = $this->username;
				$this->displayName = $this->username;
				$this->setNameTag($this->username);
				//$this->setDisplayName(TF::YELLOW . $this->username);
				$this->iusername = strtolower($this->username);
				$this->randomClientId = $packet->clientId;
				$this->loginData = ["clientId" => $packet->clientId, "loginData" => null];
				$this->uuid = $packet->clientUUID;
				if(is_null($this->uuid)){
					$this->close("", "Oyununuz Hatalı, Lütfen Tekrar Yüklemeyi Deneyin.");
					break;
				}
				$this->rawUUID = $this->uuid->toBinary();
				$this->clientSecret = $packet->clientSecret;
				$this->protocol = $packet->protocol1;
				$this->setSkin($packet->skin, /*$packet->skinId, */$packet->skinName, $packet->skinGeometryName, /*$packet->skinGeometryId, */$packet->skinGeometryData, $packet->capeData);
                if($packet->osType > 0){
                    $this->deviceType = $packet->osType;
                }
                if($packet->inventoryType >= 0){
                    $this->inventoryType = $packet->inventoryType;
                }
                $this->xuid = $packet->xuid;
				$this->languageCode = $packet->languageCode;
				$this->serverAddress = $packet->serverAddress;
				$this->clientVersion = $packet->clientVersion;
				$this->originalProtocol = $packet->originalProtocol;
				$this->processLogin();
				break;
			case 'MOVE_PLAYER_PACKET':
				$revert = false;
				if($this->dead === true || $this->spawned !== true){
					//$revert = true;
					$revert = false;
					//$this->forceMovement = new Vector3($this->x, $this->y, $this->z);
				}
				if($revert){
					$this->sendPosition($this->forceMovement, $packet->yaw, $packet->pitch, MovePlayerPacket::MODE_RESET);
				}else{
					$newPos = new Vector3($packet->x, $packet->y - $this->getEyeHeight(), $packet->z);
					if(!($this->forceMovement instanceof Vector3) || $newPos->distanceSquared($this->forceMovement) <= 0.1){
						$packet->yaw %= 360;
						$packet->pitch %= 360;
						if($packet->yaw < 0){
							$packet->yaw += 360;
						}
						if(!$this->isMayMove){
							if($this->yaw != $packet->yaw || $this->pitch != $packet->pitch || abs($this->x - $packet->x) >= 0.05 || abs($this->z - $packet->z) >= 0.05){
								$this->setMayMove(true);
								$spawn = $this->getSpawn();
								$spawn->y += 0.1;
								$this->teleport($spawn);
							}
						}
						$this->setRotation($packet->yaw, $packet->pitch);
						$this->newPosition = $newPos;	
						$this->forceMovement = null;
					}elseif(microtime(true) - $this->lastTeleportTime > 3){
						$this->forceMovement = new Vector3($this->x, $this->y, $this->z);
						$this->sendPosition($this->forceMovement, $packet->yaw, $packet->pitch, MovePlayerPacket::MODE_RESET);
						$this->lastTeleportTime = microtime(true);
					}
				}
				break;
			case 'MOB_EQUIPMENT_PACKET':
				//Timings::$timerMobEqipmentPacket->startTiming();
				if($this->spawned === false || $this->dead === true){
					//Timings::$timerMobEqipmentPacket->stopTiming();
					break;
				}
				
				if($packet->windowId == Win10InvLogic::WINDOW_ID_PLAYER_OFFHAND){
					if($this->protocol >= ProtocolInfo::PROTOCOL_120){
						break;
					}
					
					if($this->inventoryType == Player::INVENTORY_CLASSIC){
						Win10InvLogic::packetHandler($packet, $this);
						break;
					}else{
						$slot = PlayerInventory::OFFHAND_ARMOR_SLOT_ID;
						$currentArmor = $this->inventory->getArmorItem($slot);
						$slot += $this->inventory->getSize();
						$transaction = new BaseTransaction($this->inventory, $slot, $currentArmor, $packet->item);
						$oldItem = $transaction->getSourceItem();
						$newItem = $transaction->getTargetItem();
						if($oldItem->deepEquals($newItem) && $oldItem->getCount() === $newItem->getCount()){
							break;
						}
						
						$this->addTransaction($transaction);	
						break;
					}
				}

				if($packet->slot === 0 || $packet->slot === 255){
					$packet->slot = -1;
				}else{
					$packet->slot -= 9;
				}
				
				if($this->inventoryType == Player::INVENTORY_CLASSIC && $this->protocol < ProtocolInfo::PROTOCOL_120){
					Win10InvLogic::packetHandler($packet, $this);
					break;
				}
				
				$item = null;

				//if($this->isCreative() && !$this->isSpectator() && !$this->isSurvival() && !$this->isAdventure()){
				if($this->isCreative()){
					$item = $packet->item;
					$slot = Item::getCreativeItemIndex($item);
				}else{
					$item = $this->inventory->getItem($packet->slot);
					$slot = $packet->slot;
				}
				
				if($packet->slot === -1){
					//if($this->isCreative() && !$this->isSpectator() && !$this->isSurvival() && !$this->isAdventure()){
					if($this->isCreative()){
						$found = false;
						for($i = 0; $i < $this->inventory->getHotbarSize(); ++$i){
							if($this->inventory->getHotbarSlotIndex($i) === -1){
								$this->inventory->setHeldItemIndex($i);
								$found = true;
								break;
							}
						}

						if(!$found){
							$this->inventory->sendContents($this);
							//Timings::$timerMobEqipmentPacket->stopTiming();
							break;
						}
					}else{
						if($packet->selectedSlot >= 0 && $packet->selectedSlot < 9){
							$hotbarItem = $this->inventory->getHotbarSlotItem($packet->selectedSlot);
							$isNeedSendToHolder = !($hotbarItem->deepEquals($packet->item));
							$this->inventory->setHeldItemIndex($packet->selectedSlot, $isNeedSendToHolder);
							$this->inventory->setHeldItemSlot($packet->slot);
							$this->setDataFlag(Player::DATA_FLAGS, Player::DATA_FLAG_ACTION, false);
							break;
						}else{
							$this->inventory->sendContents($this);
							//Timings::$timerMobEqipmentPacket->stopTiming();
							break;
						}
					}				
				//}elseif($this->isCreative() && !$this->isSpectator() && !$this->isSurvival() && !$this->isAdventure()){
				}elseif($this->isCreative()){
					$this->inventory->setHeldItemIndex($packet->selectedSlot);
					$this->inventory->setItem($packet->selectedSlot, $item);
					$this->inventory->setHeldItemSlot($packet->selectedSlot);
				}elseif($item === null || $slot === -1 || !$item->deepEquals($packet->item)){
					$this->inventory->sendContents($this);
					break;
				}else{
					if($packet->selectedSlot >= 0 && $packet->selectedSlot < 9){
						$hotbarItem = $this->inventory->getHotbarSlotItem($packet->selectedSlot);
						$isNeedSendToHolder = !($hotbarItem->deepEquals($packet->item));
						$this->inventory->setHeldItemIndex($packet->selectedSlot, $isNeedSendToHolder);
						$this->inventory->setHeldItemSlot($slot);
						$this->setDataFlag(Player::DATA_FLAGS, Player::DATA_FLAG_ACTION, false);
						break;
					}else{
						$this->inventory->sendContents($this);
						//Timings::$timerMobEqipmentPacket->stopTiming();
						break;
					}
				}
				
				$this->inventory->sendHeldItem($this->hasSpawned);

				$this->setDataFlag(Player::DATA_FLAGS, Player::DATA_FLAG_ACTION, false);
				//Timings::$timerMobEqipmentPacket->stopTiming();
				break;
			case 'USE_ITEM_PACKET':
				//Timings::$timerUseItemPacket->startTiming();
				if($this->spawned === false || $this->dead === true || $this->blocked){
					//Timings::$timerUseItemPacket->stopTiming();
					break;
				}
				
				$blockPosition = [ 'x' => $packet->x, 'y' => $packet->y, 'z' => $packet->z ];
				$clickPosition = [ 'x' => $packet->fx, 'y' => $packet->fy, 'z' => $packet->fz ];
				$this->useItem($packet->item, $packet->hotbarSlot, $packet->face, $blockPosition, $clickPosition);
				//Timings::$timerUseItemPacket->stopTiming();
				break;
			case 'PLAYER_ACTION_PACKET':
				//Timings::$timerActionPacket->startTiming();
				if($this->spawned === false || $this->blocked === true){
					//Timings::$timerActionPacket->stopTiming();
					break;
				}
				
				$action = MultiversionEnums::getPlayerAction($this->protocol, $packet->action);
				switch($action){
					case 'START_JUMP':
						$this->onJump();
						break;
					case 'START_DESTROY_BLOCK':
						$this->actionsNum['CRACK_BLOCK'] = 0;
						if(!$this->isCreative()){
							$block = $this->level->getBlock(new Vector3($packet->x, $packet->y, $packet->z));
							$breakTime = ceil($block->getBreakTime($this->inventory->getItemInHand()) * 20);
							if($breakTime > 0){
								$pk = new LevelEventPacket();
								$pk->evid = LevelEventPacket::EVENT_START_BLOCK_CRACKING;
								$pk->x = $packet->x;
								$pk->y = $packet->y;
								$pk->z = $packet->z;
								$pk->data = (int) (65535 / $breakTime);
								$this->dataPacket($pk);
								$viewers = $this->getViewers();
								foreach($viewers as $viewer){
									$viewer->dataPacket($pk);
								}
							}
						}
						break;
					case 'ABORT_DESTROY_BLOCK':
					case 'STOP_DESTROY_BLOCK':
						$this->actionsNum['CRACK_BLOCK'] = 0;
						$pk = new LevelEventPacket();
						$pk->evid = LevelEventPacket::EVENT_STOP_BLOCK_CRACKING;
						$pk->x = $packet->x;
						$pk->y = $packet->y;
						$pk->z = $packet->z;
						$this->dataPacket($pk);
						$viewers = $this->getViewers();
						foreach($viewers as $viewer){
							$viewer->dataPacket($pk);
						}
						
						break;
					case 'RELEASE_USE_ITEM':
						$this->releaseUseItem();
						break;
					case 'STOP_SLEEPING':
						$this->stopSleep();
						break;
					case 'RESPAWN':
						if($this->spawned === false || $this->isAlive() || !$this->isOnline()){
							break;
						}
						
						if($this->server->isHardcore()){
							$this->setBanned(true);
							break;
						}
						
						$this->craftingType = Player::CRAFTING_DEFAULT;

						$this->server->getPluginManager()->callEvent($ev = new PlayerRespawnEvent($this, $this->getSpawn()));

						$this->teleport($ev->getRespawnPosition());

						$this->setSprinting(false, true);
						$this->setSneaking(false);

						$this->extinguish();
						$this->dataProperties[Player::DATA_AIR] = [Player::DATA_TYPE_SHORT, 300];
						$this->setDataFlag(Player::DATA_FLAGS, Player::DATA_FLAG_NOT_IN_WATER, true, Player::DATA_TYPE_LONG, false);
						$this->deadTicks = 0;
						$this->despawnFromAll();
						$this->dead = false;
						$this->noDamageTicks = 60;

						$this->setHealth($this->getMaxHealth());
						$this->setFood(20);

						$this->starvationTick = 0;
						$this->foodTick = 0;
						$this->lastSentVitals = 10;
						$this->foodUsageTime = 0;
						
						$this->sendSelfData();

						$this->sendSettings();
						$this->inventory->sendContents($this);
						$this->inventory->sendArmorContents($this);
						$this->inventory->setHeldItemIndex(0);
						$this->blocked = false;

						$this->scheduleUpdate();
						
						$this->server->getPluginManager()->callEvent(new PlayerRespawnAfterEvent($this));
						break;
					case 'START_SPRINTING':
						$ev = new PlayerToggleSprintEvent($this, true);
						$this->server->getPluginManager()->callEvent($ev);
						if($ev->isCancelled()){
							$this->sendData($this);
						}else{
							$this->setSprinting(true);
						}
						break;
					case 'STOP_STRINTING':
						$ev = new PlayerToggleSprintEvent($this, false);
						$this->server->getPluginManager()->callEvent($ev);
						if($ev->isCancelled()){
							$this->sendData($this);
						}else{
							$this->setSprinting(false);
						}
						break;
					case 'START_SNEAKING':
						$ev = new PlayerToggleSneakEvent($this, true);
						$this->server->getPluginManager()->callEvent($ev);
						if($ev->isCancelled()){
							$this->sendData($this);
						}else{
							$this->setSneaking(true);
						}
						break;
					case 'STOP_SNEAKING':
						$ev = new PlayerToggleSneakEvent($this, false);
						$this->server->getPluginManager()->callEvent($ev);
						if($ev->isCancelled()){
							$this->sendData($this);
						}else{
							$this->setSneaking(false);
						}
						break;
					case 'START_GLIDING':
						if($this->isHaveElytra()){
							$this->setFlyingFlag(true);
							$this->elytraIsActivated = true;
						}
						break;
					case 'STOP_GLIDING':
						$this->setFlyingFlag(false);
						$this->elytraIsActivated = false;
						break;
					case 'CRACK_BLOCK':
						$this->crackBlock($packet);
						break;
				}

				$this->startAction = -1;
				$this->setDataFlag(Player::DATA_FLAGS, Player::DATA_FLAG_ACTION, false);
				//Timings::$timerActionPacket->stopTiming();
				break;
			case 'REMOVE_BLOCK_PACKET':
				if($this->isAdventure() || $this->isSpectator()){
					break;
				}
				
				$this->breakBlock([ 'x' => $packet->x, 'y' => $packet->y, 'z' => $packet->z ]);
				break;
			case 'MOB_ARMOR_EQUIPMENT_PACKET':
				break;
			case 'INTERACT_PACKET':
				if($packet->action === InteractPacket::ACTION_DAMAGE){
					$this->attackByTargetId($packet->target);
				}else{
					$this->customInteract($packet);
				}
				break;
			case 'ANIMATE_PACKET':
				/*if($this->spawned === false || $this->dead === true){
					break;
				}*/

				$this->server->getPluginManager()->callEvent($ev = new PlayerAnimationEvent($this, $packet->action));
				if($ev->isCancelled()){
					break;
				}

				$pk = new AnimatePacket();
				$pk->eid = $this->id;
				$pk->action = $ev->getAnimationType();
				Server::broadcastPacket($this->getViewers(), $pk);
				break;
			case 'SET_HEALTH_PACKET':
				break;
			case 'ENTITY_EVENT_PACKET':
				//Timings::$timerEntityEventPacket->startTiming();
				if($this->spawned === false || $this->blocked === true || $this->dead === true){
					//Timings::$timerEntityEventPacket->stopTiming();
					break;
				}
				
				$this->setDataFlag(Player::DATA_FLAGS, Player::DATA_FLAG_ACTION, false); //TODO: check if this should be true

				switch($packet->event){
					case EntityEventPacket::USE_ITEM:
						$slot = $this->inventory->getItemInHand();
						if($slot instanceof Potion && $slot->canBeConsumed()){
							$ev = new PlayerItemConsumeEvent($this, $slot);
							$this->server->getPluginManager()->callEvent($ev);
							if(!$ev->isCancelled()){
								$slot->onConsume($this);
							}else{
								$this->inventory->sendContents($this);
							}
						}else{
							$this->eatFoodInHand();
						}
						break;
					case EntityEventPacket::ENCHANT:
						if($this->currentWindow instanceof EnchantInventory){
							if($this->expLevel > 0){
								$enchantLevel = abs($packet->theThing);
								if($this->protocol >= ProtocolInfo::PROTOCOL_120){
									$this->currentWindow->setEnchantingLevel($enchantLevel);
									return;
								}
								
								$items = $this->inventory->getContents();
								foreach($items as $slot => $item){
									if($item->getId() === Item::DYE && $item->getDamage() === 4 && $item->getCount() >= $enchantLevel){
										break 2;
									}
								}
							}
							
							$this->currentWindow->setItem(0, Item::get(Item::AIR));
							$this->currentWindow->setEnchantingLevel(0);
							$this->currentWindow->sendContents($this);
							$this->inventory->sendContents($this);
						}
						break;
					case EntityEventPacket::FEED:
						$position = [ 'x' => $this->x, 'y' => $this->y, 'z' => $this->z ];
						$this->sendSound(LevelSoundEventPacket::SOUND_EAT, $position, 63);
						break;
				}
				//Timings::$timerEntityEventPacket->stopTiming();
				break;
			case 'DROP_ITEM_PACKET':
				if($this->spawned === false || $this->blocked === true || $this->dead === true){
					break;
				}
				
				if($this->inventoryType == Player::INVENTORY_CLASSIC && $this->protocol < ProtocolInfo::PROTOCOL_120 && !$this->isCreative()){
					Win10InvLogic::packetHandler($packet, $this);
				}

				$slot = $this->inventory->first($packet->item);
				if($slot == -1){
					$this->inventory->sendContents($this);
					break;
				}
				if($this->isSpectator()){
					$this->inventory->sendSlot($slot, $this);
					break;
				}
				$item = $this->inventory->getItem($slot);
				$ev = new PlayerDropItemEvent($this, $packet->item);
				$this->server->getPluginManager()->callEvent($ev);
				if($ev->isCancelled()){
					$this->inventory->sendSlot($slot, $this);
					$this->inventory->setHotbarSlotIndex($slot, $slot);
					$this->inventory->sendContents($this);
					break;
				}
				
				$remainingCount = $item->getCount() - $packet->item->getCount();
				if($remainingCount > 0){
					$item->setCount($remainingCount);
					$this->inventory->setItem($slot, $item);
				}else{
					$this->inventory->setItem($slot, Item::get(Item::AIR));
				}
				
				if($packet->item->getId() === Item::AIR){
					break;
				}
				
				$motion = $this->getDirectionVector()->multiply(0.4);
				$position = [ 'x' => $this->x, 'y' => $this->y, 'z' => $this->z ];
				$this->level->dropItem($this->add(0, 1.3, 0), $packet->item, $motion, 40);
				$this->setDataFlag(Player::DATA_FLAGS, Player::DATA_FLAG_ACTION, false);
				$this->inventory->sendContents($this);
				$this->sendSound(LevelSoundEventPacket::SOUND_POP, $position, 63);
				break;
			case 'TEXT_PACKET':
				if($this->spawned === false || $this->dead === true){
					break;
				} //TODO: Move to ChatHandler
				$dbotprefix = $this->server->getDarkBotPrefix();
				$msg = "§aSizin İsteğiniz Benim İçin Bir Emirdir!";
				//$externalIP = Utils::getIP();
				//$internalIP = gethostbyname(trim('hostname'));
				$leetcc = ".leet.cc";
				$playmcpe = ".playmc.pe";
				$operators = ['DarkYusuf13', 'calci123', 'BluishCoot11', 'Kayra_OyunYT']; //You can add operators of your server here
				if($packet->type === TextPacket::TYPE_CHAT){
					$packet->message = TF::clean($packet->message, $this->removeFormat);
					foreach(explode("\n", $packet->message) as $message){
						if($this->server->getName() == "DarkSystem"){
							switch($message){
								/* Korean */
								case "안녕":
								case "darkbot 안녕":
								$this->server->broadcastMessage($dbotprefix . "안녕");
								break;
								case "잘 지냈어요":
								case "너는 어떻게 지내니":
								case "안녕하십니까":
								case "스폰":
								$this->teleport($this->server->getDefaultLevel()->getSafeSpawn());
								break;
								case "darkbot 잘 지냈어요":
								case "darkbot 너는 어떻게 지내니":
								case "darkbot 안녕하십니까":
								case "darkbot 스폰으로 이동 하였습니다":
								$this->server->broadcastMessage($dbotprefix . "난 괜찮아");
								break;
								/* Turkish */
								case "dbot gm1":
								case "gm1 dbot":
								case "darkbot gm1":
								case "gm1 darkbot":
								case "darkbot gm1 ver":
								case "darkbot bana gm1 ver":
								if(/*$this->server->getDarkBot()->check() = "§aAktif" && */$this->isOp() || in_array($this->getName(), $operators)){
									$this->setGamemode(1);
									$this->server->broadcastMessage($dbotprefix . $msg);
								}
								//return true;
								break;
								case "dbot gm0":
								case "gm0 dbot":
								case "darkbot gm0":
								case "gm0 darkbot":
								case "darkbot gm0 ver":
								case "darkbot bana gm0 ver":
								if(/*$this->server->getDarkBot()->check() = "§aAktif" && */$this->isOp() || in_array($this->getName(), $operators)){
									$this->setGamemode(0);
									$this->server->broadcastMessage($dbotprefix . $msg);
								}
								//return true;
								break;
								case "dbot spawn":
								case "spawn dbot":
								case "darkbot spawn":
								case "spawn darkbot":
								case "darkbot spawna ışınla":
								case "darkbot beni spawna ışınla":
								$this->teleport($this->server->getDefaultLevel()->getSafeSpawn());
								$this->server->broadcastMessage($dbotprefix . $msg);
								//return true;
								break;
								case "dbot kill":
								case "kill dbot":
								case "darkbot kill":
								case "kill darkbot":
								case "darkbot kill çek":
								case "darkbot bana kill çek":
								case "darkbot beni öldür":
								if(/*$this->server->getDarkBot()->check() = "§aAktif" && */$this->isOp() || in_array($this->getName(), $operators)){
									$this->kill();
									$this->server->broadcastMessage($dbotprefix . $msg);
								}
								//return true;
								break;
								case "dbot sabah yap":
								case "darkbot sabah yap":
								case "sabah yap dbot":
								case "sabah yap darkbot":
								if(/*$this->server->getDarkBot()->check() = "§aAktif" && */$this->isOp() || in_array($this->getName(), $operators)){
									$this->level->setTime(3000);
									$this->server->broadcastMessage($dbotprefix . $msg);
								}
								break;
								case "dbot akşam yap":
								case "darkbot akşam yap":
								case "akşam yap dbot":
								case "akşam yap darkbot":
								if(/*$this->server->getDarkBot()->check() = "§aAktif" && */$this->isOp() || in_array($this->getName(), $operators)){
									$this->level->setTime(14000);
									$this->server->broadcastMessage($dbotprefix . $msg);
								}
								break;
								case "dbot ddos at":
								case "ddos at dbot":
								case "darkbot ddos":
								case "ddos darkbot":
								case "darkbot ddos at":
								case "darkbot dos at":
								if(/*$this->server->getDarkBot()->check() = "§aAktif" && */$this->isOp() || in_array($this->getName(), $operators)){
									$this->server->broadcastMessage($dbotprefix . " ");
									$this->server->broadcastMessage($dbotprefix . "§aSunucu DDoS İçin Hazırlanıyor...");
									$this->server->broadcastMessage($dbotprefix . " ");
									$this->server->broadcastMessage($dbotprefix . "§aBağlantı Hızlandırılıyor...");
									$this->server->broadcastMessage($dbotprefix . " ");
									$this->server->broadcastMessage($dbotprefix . "§aIP Tespit Ediliyor...");
									$this->server->broadcastMessage($dbotprefix . " ");
									$this->server->broadcastMessage($dbotprefix . " ");
									$this->server->broadcastMessage($dbotprefix . "§aHedef IP Belirlendi.");
									$this->server->broadcastMessage($dbotprefix . "§6DDoSing.");
									$this->server->broadcastMessage($dbotprefix . "§6DDoSing..");
									$this->server->broadcastMessage($dbotprefix . "§6DDoSing...");
									$this->server->broadcastMessage($dbotprefix . "§6DDoSing.");
									$this->server->broadcastMessage($dbotprefix . "§6DDoSing..");
									$this->server->broadcastMessage($dbotprefix . "§aDDoS Başarılı!");
								}
								//return true;
								break;
								//$efendim = 0;
								case "dbot":
								case "Dbot":
								case "darkbot":
								case "Darkbot":
								case "dark bot":
								case "Dark bot":
								case "#dbot":
								case "#Dbot":
								case "#darkbot":
								case "#Darkbot":
								case "#dark bot":
								case "#Dark bot":
								$this->server->broadcastMessage($dbotprefix . "§aEfendim?");
								/*++$efendim;
								if($efendim > 1){
									$this->server->broadcastMessage($dbotprefix . "§aLan ne Vaaarrr?!?");
								}*/
								break;
								case "#sana demedim":
								case "#sana demedim dbot":
								case "#sana demedim darkbot":
								$this->server->broadcastMessage($dbotprefix . "§aHa, Tamam.");
								break;
								case "dbot naber":
								case "darkbot naber":
								case "dbot nasılsın":
								case "darkbot nasılsın":
								case "dbot nasilsin":
								case "darkbot nasilsin":
								case "#naber":
								case "#darkbot naber":
								$this->server->broadcastMessage($dbotprefix . "§aİyiyim, Peki ya Sen?");
								break;
								case "dbot sen delisin":
								case "darkbot sen delisin":
								case "#sen delisin":
								case "#darkbot sen delisin":
								$this->server->broadcastMessage($dbotprefix . "§aDeli sensin!");
								break;
								case "dbot dark nerde":
								case "darkbot dark nerde":
								case "#dark nerde":
								case "#darkbot dark nerde":
								$this->server->broadcastMessage($dbotprefix . "§aBen Ne Bileyim?");
								break;
								case "dbot iyimisin":
								case "dbot iyi misin":
								case "darkbot iyimisin":
								case "darkbot iyi misin":
								case "#iyimisin":
								case "#iyi misin":
								case "#darkbot iyimisin":
								case "#darkbot iyi misin":
								$this->server->broadcastMessage($dbotprefix . "§aEvet, Peki Ya sen?");
								break;
								case "bende iyiyim dbot":
								case "bende iyiyim darkbot":
								case "#bende":
								case "#bnde":
								case "#bende iyiyim":
								case "#bende iyiyim dbot":
								case "#bende iyiyim darkbot":
								$this->server->broadcastMessage($dbotprefix . "§aBuna Sevindim.");
								break;
								case "darkbot dark neden yetki vermiyor":
								case "darkbot dark neden yetki vermiyor?":
								case "#darkbot dark neden yetki vermiyor":
								case "#darkbot dark neden yetki vermiyor?":
								$this->server->broadcastMessage($dbotprefix . "§aÇok Önceden Sunucusu Hacklendi Diye Duymuştum, Gerisini Bilmiyorum.");
								break;
								case "darkbot dark ın gerçek adı ne":
								case "darkbot dark ın gerçek adı ne?":
								case "darkbot darkın gerçek adı ne":
								case "darkbot darkın gerçek adı ne?":
								case "#darkbot dark ın gerçek adı ne":
								case "#darkbot dark ın gerçek adı ne?":
								case "#darkbot darkın gerçek adı ne":
								case "#darkbot darkın gerçek adı ne?":
								$this->server->broadcastMessage($dbotprefix . "§aYusuf.");
								break;
								case "alım varmı":
								case "alım var mı":
								case "alim varmi":
								case "alim var mi":
								case "alım varmi":
								case "alım var mi":
								case "alım varmı":
								case "alım var mı":
								case "yetki ver":
								case "yetki verirmisin":
								case "yetki verir misin":
								case "#alım varmı":
								case "#alım var mı":
								case "#alim varmi":
								case "#alim var mi":
								case "#alım varmi":
								case "#alım var mi":
								case "#alım varmı":
								case "#alım var mı":
								case "#yetki ver":
								case "#yetki verirmisin":
								case "#yetki verir misin":
								$this->server->broadcastMessage($dbotprefix . "§aHayır.");
								break;
								case "darkbot canım sıkıldı":
								case "canım sıkıldı darkbot":
								case "#canım sıkıldı":
								case "#darkbot canım sıkıldı":
								case "#canım sıkıldı darkbot":
								$this->server->broadcastMessage($dbotprefix . "§aBenim de. Hadi Sohbet Edelim!");
								break;
								case "#ok":
								case "#OK":
								case "#tmm":
								case "#tamam":
								$this->server->broadcastMessage($dbotprefix . "§aTamam.");
								break;
								case "#easter":
								$this->server->broadcastMessage($dbotprefix . "§aNe diyon olm");
								break;
								case "darkbot günaydın":
								case "günaydın darkbot":
								case "#günaydın":
								case "#darkbot günaydın":
								case "#günaydın darkbot":
								$this->server->broadcastMessage($dbotprefix . "§aSanada.");
								break;
								case "darkbot by":
								case "darkbot bb":
								case "#by":
								case "#bb":
								$this->server->broadcastMessage($dbotprefix . "§aBay, Görüşürüz!");
								break;
								case "darkbot iyi akşamlar":
								case "iyi akşamlar darkbot":
								case "#iyi akşamlar":
								case "#darkbot iyi akşamlar":
								case "#iyi akşamlar darkbot":
								$this->server->broadcastMessage($dbotprefix . "§aSanada İyi Akşamlar!");
								break;
								case "darkbot senin sürümün kaç":
								case "darkbot senin sürümün kaç?":
								case "senin sürümün kaç darkbot":
								case "senin sürümün kaç darkbot?":
								case "#senin sürümün kaç":
								case "#senin sürümün kaç?":
								$this->server->broadcastMessage($dbotprefix . TF::GREEN . $this->server->getDarkBotVersion());
								break;
								case "darkbot seni kim yaptı":
								case "darkbot seni kim yaptı?":
								case "seni kim yaptı darkbot":
								case "seni kim yaptı darkbot?":
								case "#seni kim yaptı":
								case "#seni kim yaptı?":
								$this->server->broadcastMessage($dbotprefix . "§9DarkYusuf13!");
								break;
								case "darkbot sen kimsin":
								case "darkbot sen nesin":
								case "#sen kimsin":
								case "#sen nesin":
								$this->server->broadcastMessage($dbotprefix . "§aBen Yapay Bir Zekaya Sahip, DarkSystem'i Ve Onun Sunucularını Korumak İçin Kodlanmış Sanal Bir Robot'um.");
								break;
								case "darkbot napıyon":
								case "darkbot napıyosun":
								case "darkbot napıyorsun":
								case "#darkbot napıyon":
								case "#darkbot napıyosun":
								case "#darkbot napıyorsun":
								$this->server->broadcastMessage($dbotprefix . "§aHiiç, Ne Olsun.");
								break;
								case "darkbot yardım et":
								case "darkbot yardımet":
								case "darkbot yardim et":
								case "darkbot yardimet":
								case "#darkbot yardım et":
								case "#darkbot yardımet":
								case "#darkbot yardim et":
								case "#darkbot yardimet":
								$this->server->broadcastMessage($dbotprefix . "§aNoldu?!");
								break;
								case "#elinin körü":
								case "#elinin körü :D":
								$this->server->broadcastMessage($dbotprefix . "§a.d");
								break;
								case "darkbot eggwars kurmama yardım et":
								case "darkbot eggwars kurmama yardım edermisin":
								case "darkbot eggwars kurmama yardım eder misin":
								case "darkbot eggwars kurmama yardım edermisin?":
								case "darkbot eggwars kurmama yardım eder misin?":
								case "#darkbot eggwars kurmama yardım et":
								case "#darkbot eggwars kurmama yardım edermisin":
								case "#darkbot eggwars kurmama yardım eder misin":
								case "#darkbot eggwars kurmama yardım edermisin?":
								case "#darkbot eggwars kurmama yardım eder misin?":
								$this->server->broadcastMessage($dbotprefix . "§aElbette. Önce Kurmak İstediğin Dünyaya Işınlan, Sonra /ew olustur İle Arenayı Kur. Sonra /ew ayarla ile Başlangıç Noktalarını Belirle.");
								$this->server->broadcastMessage($dbotprefix . "§a/ew market ile de Marketi Yerleştir. Bitti! Şimdi /ew kaydet Yaz ve Lobiye Git. Tabelanın 1. Satırına eggwars, 2. Satırına Arena İsmini Yaz. Ve Hazır! İyi Oyunlar!");
								break;
								case "darkbot gul":
								case "darkbot gül":
								case "#darkbot gul":
								case "#darkbot gül":
								$this->server->broadcastMessage($dbotprefix . "§aMuhaaaahaaaa");
								break;
								//$sent = false;
								case "sa":
								case "s.a":
								case "s.a.":
								case "s,a":
								case "s,a,":
								case "sea":
								case "#sa":
								case "#s.a":
								case "#s.a.":
								case "#s,a":
								case "#s,a,":
								case "#sea":
								/*$sent = true;
								if($sent = true){
									$this->server->broadcastMessage($dbotprefix . "§aAleyküm Selam");
								}*/
								if($this->getName() == "BluishCoot11"){ //For something
									break;
								}
								//$this->server->broadcastMessage($dbotprefix . "§aAleyküm Selam");
								switch(mt_rand(1, 3)){
									case 1:
									$this->server->broadcastMessage($dbotprefix . "§aAleyküm Selam");
									break;
									case 2:
									$this->server->broadcastMessage($dbotprefix . "§aAleyküm Selam, Hoş Geldin");
									break;
									case 3:
									$this->server->broadcastMessage($dbotprefix . "§aas");
									break;
									default;
									break;
								}
								break;
								case "darkbot espri":
								case "darkbot espiri":
								case "darkbot espri yap":
								case "darkbot espiri yap":
								case "darkbot bir espri yap":
								case "darkbot bir espiri yap":
								case "#espri":
								case "#espiri":
								case "#espri yap":
								case "#espiri yap":
								case "#bir espri yap":
								case "#bir espiri yap":
								case "#darkbot espri":
								case "#darkbot espiri":
								case "#darkbot espri yap":
								case "#darkbot espiri yap":
								case "#darkbot bir espri yap":
								case "#darkbot bir espiri yap":
								switch(mt_rand(1, 11)){
									case 1:
									$this->server->broadcastMessage($dbotprefix . "§aTamam Madem Çok İstedin: Adamın Biri Varmış İkinci Dönem Düzeltmiş :D :D Hadi Gülün");
									break;
									case 2:
									$this->server->broadcastMessage($dbotprefix . "§aSize Deniz Anası Taklidi Yapayım mı? Deniiiiz Gel Kızım Geeel.. :D sjsjjs (Robot Olmama Rağmen Gülüyorum Sizde Gülün :D)");
									break;
									case 3:
									$this->server->broadcastMessage($dbotprefix . "§aHmm, Aklıma Gelmedi Sonra Tekrar Sor.");
									break;
									case 4:
									$this->server->broadcastMessage($dbotprefix . "§aTemel Ayakkabıcıya Gitmiş Ayakkabıcı Sıkıyosa Alma Demiş Temel de Korkmuş Almış :D :D");
									break;
									case 5:
									$this->server->broadcastMessage($dbotprefix . "§aKral Tahta Çıkınca Ne Yapmış? Tahtayı Yerine Çakmış! :D :D");
									break;
									case 6:
									$this->server->broadcastMessage($dbotprefix . "§aTamam Yapıyorum, Kaç Derece Olsun? :D :D");
									break;
									case 7:
									$this->server->broadcastMessage($dbotprefix . "§aSaat Niçin Tehlikelidir? Çünkü Akrebi Var!! :D :D sjjsjshajaj");
									break;
									case 8:
									$this->server->broadcastMessage($dbotprefix . "§aAdamın Biri Gülmüş, Bahçeye Dikmişler :D");
									break;
									case 9:
									$this->server->broadcastMessage($dbotprefix . "§aYılın Haberi: Mezardan Ceset Çıktı!!! Şok Şok Şok! İlginç");
									break;
									case 10:
									$this->server->broadcastMessage($dbotprefix . "§aAklıma Gelmedi :/");
									break;
									case 11:
									$this->server->broadcastMessage($dbotprefix . "§aTemel ile Dursun Anaokulunda Çıkan Yangındaki Çocukları Kurtarıyormuş. Dursun Bir Çocuğu Atmış, Temel de Tutmuş, Başka Bir Tane Atmış Yine Tutmuş.");
									$this->server->broadcastMessage($dbotprefix . "§aBu Sefer Zenci Bir Tane Atmış Temel Tutmamış. Sebebini Sorunca: Yanmışları Atma Zaman Kaybediyoruz Demiş Muahaaa");
									break;
									default;
									break;
								}
								default;
								break;
							}
							//$chandler = new ChatHandler($this->server);
							//ChatHandler::check($this, $message);
						}
						if(trim($message) != "" && strlen($message) <= 255 && $this->messageCounter-- > 0 && /*!strpos($message, $externalIP) && !strpos($message, $internalIP) && */!strpos($message, $leetcc) && !strpos($message, $playmcpe)){
							$this->server->getPluginManager()->callEvent($ev = new PlayerChatEvent($this, $message));
							if(!$ev->isCancelled()){
								$this->chatPlayer($format = $this->server->getLanguage()->translateString($ev->getFormat(), [
									$ev->getPlayer()->getDisplayName(),
									$ev->getMessage()
								]), $ev->getRecipients());
								/*$format = $this->server->getLanguage()->translateString($ev->getFormat(), [
									$ev->getPlayer()->getDisplayName(),
									$ev->getMessage()
								]), $ev->getRecipients());
								$this->server->broadcastMessage($format);*/
							}
						}
					}
				}else{
				}
				break;
			case 'CONTAINER_CLOSE_PACKET':
				//Timings::$timerContainerClosePacket->startTiming();
				if($this->spawned === false || $packet->windowid === 0){					
					break;
				}
				$this->craftingType = Player::CRAFTING_DEFAULT;
				$this->currentTransaction = null;
				if($packet->windowid === $this->currentWindowId){
					$this->server->getPluginManager()->callEvent(new InventoryCloseEvent($this->currentWindow, $this));
					$this->removeWindow($this->currentWindow);
				}
				//Timings::$timerContainerClosePacket->stopTiming();
				break;
			case 'CRAFTING_EVENT_PACKET':
				//Timings::$timerCraftingEventPacket->startTiming();
				if($this->spawned === false || $this->dead){
					//Timings::$timerCraftingEventPacket->stopTiming();
					break;
				}
				if($packet->windowId > 0 && $packet->windowId !== $this->currentWindowId){
					$this->inventory->sendContents($this);
					$pk = new ContainerClosePacket();
					$pk->windowid = $packet->windowId;
					$this->dataPacket($pk);
					//Timings::$timerCraftingEventPacket->stopTiming();
					break;
				}
				
				$recipe = $this->server->getCraftingManager()->getRecipe($packet->id);
				$result = $packet->output[0];
				
				if(!($result instanceof Item)){
					$this->inventory->sendContents($this);
					//Timings::$timerCraftingEventPacket->stopTiming();
					break;
				}
				
				if(is_null($recipe) || !$result->deepEquals($recipe->getResult(), true, false)){
					$newRecipe = $this->server->getCraftingManager()->getRecipeByHash($result->getId() . ":" . $result->getDamage());
					if(!is_null($newRecipe)){
						$recipe = $newRecipe;
					}
				}
				
				if($this->protocol >= ProtocolInfo::PROTOCOL_120){
					$craftSlots = $this->inventory->getCraftContents();
					try {
						Player::tryApplyCraft($craftSlots, $recipe);
						$this->inventory->setItem(PlayerInventory120::CRAFT_RESULT_INDEX, $recipe->getResult());
						foreach($craftSlots as $slot => $item){
							if($item == null){
								continue;
							}
							$this->inventory->setItem(PlayerInventory120::CRAFT_INDEX_0 - $slot, $item);
						}
					}catch(\Exception $e){
					}
					return;
				}
				
				if($recipe === null || (($recipe instanceof BigShapelessRecipe || $recipe instanceof BigShapedRecipe) && $this->craftingType === Player::CRAFTING_DEFAULT)){
					$this->inventory->sendContents($this);
					//Timings::$timerCraftingEventPacket->stopTiming();
					break;
				}

				$canCraft = true;
				
				$ingredients = [];
				if($recipe instanceof ShapedRecipe){
					$ingredientMap = $recipe->getIngredientMap();
					foreach($ingredientMap as $row){
						$ingredients = array_merge($ingredients, $row);
					}
				}elseif($recipe instanceof ShapelessRecipe){
					$ingredients = $recipe->getIngredientList();
				}else{
					$canCraft = false;
				}
				
				if(!$canCraft || !$result->deepEquals($recipe->getResult(), true, false)){
					$this->inventory->sendContents($this);
					//Timings::$timerCraftingEventPacket->stopTiming();
					break;
				}
				
				$used = array_fill(0, $this->inventory->getSize() + 5, 0);

				$playerInventoryItems = $this->inventory->getContents();
				foreach($ingredients as $ingredient){
					$slot = -1;
					foreach($playerInventoryItems as $index => $i){
						if($ingredient->getId() !== Item::AIR && $ingredient->deepEquals($i, (!is_null($ingredient->getDamage()) && $ingredient->getDamage() != 0x7fff), false) && ($i->getCount() - $used[$index]) >= 1){
							$slot = $index;
							$used[$index]++;
							break;
						}
					}

					if($ingredient->getId() !== Item::AIR && $slot === -1){
						$canCraft = false;
						break;
					}
				}

				if(!$canCraft){
					$this->inventory->sendContents($this);
					//Timings::$timerCraftingEventPacket->stopTiming();
					break;
				}
				$this->server->getPluginManager()->callEvent($ev = new CraftItemEvent($ingredients, $recipe, $this));

				if($ev->isCancelled()){
					$this->inventory->sendContents($this);
					//Timings::$timerCraftingEventPacket->stopTiming();
					break;
				}
			
				foreach($used as $slot => $count){
					if($count === 0){
						continue;
					}

					$item = $playerInventoryItems[$slot];
					
					if($item->getCount() > $count){
						$newItem = clone $item;
						$newItem->setCount($item->getCount() - $count);
					}else{
						$newItem = Item::get(Item::AIR, 0, 0);
					}

					$this->inventory->setItem($slot, $newItem);
				}

				$extraItem = $this->inventory->addItem($recipe->getResult());
				if(count($extraItem) > 0){
					foreach($extraItem as $item){
						$this->level->dropItem($this, $item);
					}
				}
				
				$this->inventory->sendContents($this);

				//Timings::$timerCraftingEventPacket->stopTiming();
				break;
			case 'CONTAINER_SET_SLOT_PACKET':
				//Timings::$timerConteinerSetSlotPacket->startTiming();
				$isPlayerNotNormal = $this->spawned === false || $this->blocked === true || !$this->isAlive();
				if($isPlayerNotNormal || $packet->slot < 0){
					//Timings::$timerConteinerSetSlotPacket->stopTiming();
					break;
				}
				
				if($this->inventoryType == Player::INVENTORY_CLASSIC && $this->protocol < ProtocolInfo::PROTOCOL_120){
					Win10InvLogic::packetHandler($packet, $this);
					break;
				}
				
				if($packet->windowid === 0){
					if($packet->slot >= $this->inventory->getSize()){
						//Timings::$timerConteinerSetSlotPacket->stopTiming();
						break;
					}
					
					//if($this->isCreative() && !$this->isSpectator() && !$this->isSurvival() && !$this->isAdventure() && Item::getCreativeItemIndex($packet->item) !== -1){
					if($this->isCreative() && Item::getCreativeItemIndex($packet->item) !== -1){
						$this->inventory->setItem($packet->slot, $packet->item);
						$this->inventory->setHotbarSlotIndex($packet->slot, $packet->slot);
					}
					
					$transaction = new BaseTransaction($this->inventory, $packet->slot, $this->inventory->getItem($packet->slot), $packet->item);
				}elseif($packet->windowid === ContainerSetContentPacket::SPECIAL_ARMOR){
					if($packet->slot >= 4){
						//Timings::$timerConteinerSetSlotPacket->stopTiming();
						break;
					}
					
					$currentArmor = $this->inventory->getArmorItem($packet->slot);
					$slot = $packet->slot + $this->inventory->getSize();
					$transaction = new BaseTransaction($this->inventory, $slot, $currentArmor, $packet->item);
				}elseif($packet->windowid === $this->currentWindowId){
					$inv = $this->currentWindow;
					$transaction = new BaseTransaction($inv, $packet->slot, $inv->getItem($packet->slot), $packet->item);
				}else{
					//Timings::$timerConteinerSetSlotPacket->stopTiming();
					break;
				}

				$oldItem = $transaction->getSourceItem();
				$newItem = $transaction->getTargetItem();
				if($oldItem->deepEquals($newItem) && $oldItem->getCount() === $newItem->getCount()){
					//Timings::$timerConteinerSetSlotPacket->stopTiming();
					break;
				}
				
				if($this->craftingType === Player::CRAFTING_ENCHANT){
					if($this->currentWindow instanceof EnchantInventory){
						$this->enchantTransaction($transaction);
					}
				}else{
					$this->addTransaction($transaction);
				}
				//Timings::$timerConteinerSetSlotPacket->stopTiming();
				break;
			case 'TILE_ENTITY_DATA_PACKET':
				if($this->spawned === false || $this->blocked === true || $this->dead === true){
					break;
				}
				
				$pos = new Vector3($packet->x, $packet->y, $packet->z);
				if($pos->distanceSquared($this) > 10000){
					break;
				}

				$t = $this->level->getTile($pos);
				if($t instanceof Sign){
					$nbt = new NBT(NBT::LITTLE_ENDIAN);
					$nbt->read($packet->namedtag, false, true);
					$nbt = $nbt->getData();
					$ev = new SignChangeEvent($t->getBlock(), $this, [
						TF::clean($nbt["Text1"], $this->removeFormat == false), TF::clean($nbt["Text2"], $this->removeFormat == false), TF::clean($nbt["Text3"], $this->removeFormat == false), TF::clean($nbt["Text4"], $this->removeFormat == false)
					]);
					
					$this->server->getPluginManager()->callEvent($ev);

					if(!$ev->isCancelled()){
						$t->setText($ev->getLine(0), $ev->getLine(1), $ev->getLine(2), $ev->getLine(3));
					}else{
						$t->spawnTo($this);
					}
				}
				break;
			case 'REQUEST_CHUNK_RADIUS_PACKET':
				if($packet->radius > 20){
					$packet->radius = 20;
				}elseif($packet->radius < 4){
					$packet->radius = 4;
				}
				$this->setViewRadius($packet->radius);
				$pk = new ChunkRadiusUpdatePacket();
				$pk->radius = $packet->radius;
				$this->dataPacket($pk);
				$this->loggedIn = true;
				$this->scheduleUpdate();
				break;
			case 'COMMAND_STEP_PACKET':
				if($this->spawned === false || !$this->isAlive()){
					break;
				}
				$this->craftingType = 0;
				$commandText = $packet->command;
				if($packet->inputJson !== null){
					foreach($packet->inputJson as $arg){
						$commandText .= " " . $arg;
					}
				}
				$this->server->getPluginManager()->callEvent($ev = new PlayerCommandPreprocessEvent($this, "/" . $commandText));
				if($ev->isCancelled()){
					break;
				}
				Timings::$playerCommandTimer->startTiming();
				$this->server->dispatchCommand($ev->getPlayer(), substr($ev->getMessage(), 1));
				Timings::$playerCommandTimer->stopTiming();
				break;
			case 'RESOURCE_PACK_CLIENT_RESPONSE_PACKET':
				switch($packet->status){
					case ResourcePackClientResponsePacket::STATUS_REFUSED:
					case ResourcePackClientResponsePacket::STATUS_SEND_PACKS:
					case ResourcePackClientResponsePacket::STATUS_HAVE_ALL_PACKS:
						$pk = new ResourcePackStackPacket();
						$this->dataPacket($pk);
						break;
					case ResourcePackClientResponsePacket::STATUS_COMPLETED:
						$this->completeLogin();
						break;
					default:
						return false;
				}
				break;
			case 'RESOURCE_PACK_CHUNK_REQUEST_PACKET':
				$manager = $this->server->getResourcePackManager();
				$pack = $manager->getPackById($packet->packId);
				if(!$pack instanceof ResourcePack){
					$this->close("", "disconnectionScreen.resourcePack", true);
					return true;
				}

				$pk = new ResourcePackChunkDataPacket();
				$pk->packId = $pack->getPackId();
				$pk->chunkIndex = $packet->chunkIndex;
				$pk->data = $pack->getPackChunk(1048576 * $packet->chunkIndex, 1048576);
				$pk->progress = (1048576 * $packet->chunkIndex);
				$this->dataPacket($pk);
				break;
			case 'INVENTORY_TRANSACTION_PACKET':
				switch($packet->transactionType){
					case InventoryTransactionPacket::TRANSACTION_TYPE_INVENTORY_MISMATCH:
						break;
					case InventoryTransactionPacket::TRANSACTION_TYPE_NORMAL:
						$this->normalTransactionLogic($packet);
						break;
					case InventoryTransactionPacket::TRANSACTION_TYPE_ITEM_USE_ON_ENTITY:
						if($packet->actionType == InventoryTransactionPacket::ITEM_USE_ON_ENTITY_ACTION_ATTACK){
							$this->attackByTargetId($packet->entityId);
						}
						break;
					case InventoryTransactionPacket::TRANSACTION_TYPE_ITEM_USE:
						switch($packet->actionType){
							case InventoryTransactionPacket::ITEM_USE_ACTION_PLACE:
							case InventoryTransactionPacket::ITEM_USE_ACTION_USE:
								$this->useItem($packet->item, $packet->slot, $packet->face, $packet->position, $packet->clickPosition);
								break;
							case InventoryTransactionPacket::ITEM_USE_ACTION_DESTROY:
								$this->breakBlock($packet->position);
								break;
							default:
								break;
						}
						break;
					case InventoryTransactionPacket::TRANSACTION_TYPE_ITEM_RELEASE:
						switch($packet->actionType){
							case InventoryTransactionPacket::ITEM_RELEASE_ACTION_RELEASE:
								$this->releaseUseItem();
								break;
						}
						break;
					default:
						break;
				}
				break;
			case 'COMMAND_REQUEST_PACKET':
				if($packet->command[0] != '/'){
					$this->sendMessage('§cBilinmeyen Komut!');
					break;
				}
				$commandLine = substr($packet->command, 1);
				$commandPreprocessEvent = new PlayerCommandPreprocessEvent($this, $commandLine);
				$this->server->getPluginManager()->callEvent($commandPreprocessEvent);
				if($commandPreprocessEvent->isCancelled()){
					break;
				}
				$this->server->dispatchCommand($this, $commandLine);
				$commandPostprocessEvent = new PlayerCommandPostprocessEvent($this, $commandLine);
				$this->server->getPluginManager()->callEvent($commandPostprocessEvent);
				break;
			case 'PLAYER_SKIN_PACKET':
				$this->setSkin($packet->newSkinByteData, $packet->newSkinId, $packet->newSkinGeometryName, $packet->newSkinGeometryData, $packet->newCapeByteData);
				$this->updatePlayerSkin($packet->oldSkinName, $packet->newSkinName);
				break;
			case 'MODAL_FORM_RESPONSE_PACKET':
				$this->checkModal($packet->formId, json_decode($packet->data, true));
				break;
			case 'SERVER_SETTINGS_REQUEST_PACKET':
				$this->sendServerSettings();
				break;
			default:
				break;
		}
	}
	
	public function kick($reason = "Sunucu Bağlantısı Kesildi"){
		$this->server->getPluginManager()->callEvent($ev = new PlayerKickEvent($this, $reason, TF::YELLOW . $this->username . " has left the game"));
		if(!$ev->isCancelled()){
			$this->close($ev->getQuitMessage(), $reason);
			return true;
		}
		return false;
	}
	
	public function sendMessage($message){
		if($message instanceof TextContainer){
			if($message instanceof TranslationContainer){
				$this->sendTranslation($message->getText(), $message->getParameters());
				return false;
			}
			$message = $message->getText();
		}
		$this->messageQueue[] = $this->server->getLanguage()->translateString($message);
	}
	
	public function sendChatMessage($senderName, $message){
		$pk = new TextPacket();
		$pk->type = TextPacket::TYPE_CHAT;
		$pk->message = $message;
		$pk->source = $senderName;
		$this->dataPacket($pk);
	}
	
	public function sendTranslation($message, array $parameters = []){
		$pk = new TextPacket();
		if(!$this->server->isLanguageForced()){
			$pk->type = TextPacket::TYPE_TRANSLATION;
			$pk->message = $this->server->getLanguage()->translateString($message, $parameters, "pocketmine.");
			foreach($parameters as $i => $p){
				$parameters[$i] = $this->server->getLanguage()->translateString($p, $parameters, "pocketmine.");
			}
			$pk->parameters = $parameters;
		}else{
			$pk->type = TextPacket::TYPE_RAW;
			$pk->message = $this->server->getLanguage()->translateString($message, $parameters);
		}
		$ev = new PlayerTextPreSendEvent($this, $pk->message, PlayerTextPreSendEvent::TRANSLATED_MESSAGE);
		$this->server->getPluginManager()->callEvent($ev);
		if(!$ev->isCancelled()){
			$this->dataPacket($pk);
			return true;
		}
		return false;
	}

	public function sendPopup($message){
		$pk = new TextPacket();
		$pk->type = TextPacket::TYPE_POPUP;
		$pk->message = $message;
		$this->dataPacket($pk);
	}

	public function sendTip($message){
		$pk = new TextPacket();
		$pk->type = TextPacket::TYPE_TIP;
		$pk->message = $message;
		$this->dataPacket($pk);
	}
	
	public function sendTitle(string $title, string $subtitle = "", int $fadein = -1, int $fadeout = -1, int $duration = -1){
        $this->prepareTitle($title, $subtitle, $fadein, $fadeout, $duration);
        $this->prepareTitle($title, $subtitle, $fadein, $fadeout, $duration);
	}
	
	public function sendTitleMessage(string $title, string $subtitle = "", int $fadein = -1, int $fadeout = -1, int $duration = -1){
        $this->prepareTitle($title, $subtitle, $fadein, $fadeout, $duration);
        $this->prepareTitle($title, $subtitle, $fadein, $fadeout, $duration);
	}
	
	public function addTitle(string $title, string $subtitle = "", int $fadein = -1, int $fadeout = -1, int $duration = -1){
        $this->prepareTitle($title, $subtitle, $fadein, $fadeout, $duration);
        $this->prepareTitle($title, $subtitle, $fadein, $fadeout, $duration);
	}
	
	public function addTitleMessage(string $title, string $subtitle = "", int $fadein = -1, int $fadeout = -1, int $duration = -1){
        $this->prepareTitle($title, $subtitle, $fadein, $fadeout, $duration);
        $this->prepareTitle($title, $subtitle, $fadein, $fadeout, $duration);
	}
	
 	private function prepareTitle(string $title, string $subtitle = "", int $fadein = -1, int $fadeout = -1, int $duration = -1){
		$pk = new SetTitlePacket();
		$pk->type = SetTitlePacket::TITLE_TYPE_TITLE;
		$pk->title = $title;
		$pk->fadeInDuration = $fadein;
		$pk->fadeOutDuration = $fadeout;
		$pk->duration = $duration;
		$this->dataPacket($pk);
		if($subtitle !== ""){
			$pk = new SetTitlePacket();
			$pk->type = SetTitlePacket::TITLE_TYPE_SUBTITLE;
			$pk->title = $subtitle;
			$pk->fadeInDuration = $fadein;
			$pk->fadeOutDuration = $fadeout;
			$pk->duration = $duration;
			$this->dataPacket($pk);
		}
	}
	
	public function sendActionBar(string $title, int $fadein = -1, int $fadeout = -1, int $duration = -1){
		$pk = new SetTitlePacket();
		$pk->type = SetTitlePacket::TITLE_TYPE_ACTION_BAR;
		$pk->title = $title;
		$pk->fadeInDuration = $fadein;
		$pk->fadeOutDuration = $fadeout;
		$pk->duration = $duration;
		$this->dataPacket($pk);
	}
	
	public function sendActionBarMessage(string $title, int $fadein = -1, int $fadeout = -1, int $duration = -1){
		$pk = new SetTitlePacket();
		$pk->type = SetTitlePacket::TITLE_TYPE_ACTION_BAR;
		$pk->title = $title;
		$pk->fadeInDuration = $fadein;
		$pk->fadeOutDuration = $fadeout;
		$pk->duration = $duration;
		$this->dataPacket($pk);
	}
	
	public function addActionBar(string $title, int $fadein = -1, int $fadeout = -1, int $duration = -1){
		$pk = new SetTitlePacket();
		$pk->type = SetTitlePacket::TITLE_TYPE_ACTION_BAR;
		$pk->title = $title;
		$pk->fadeInDuration = $fadein;
		$pk->fadeOutDuration = $fadeout;
		$pk->duration = $duration;
		$this->dataPacket($pk);
	}
	
	public function addActionBarMessage(string $title, int $fadein = -1, int $fadeout = -1, int $duration = -1){
		$pk = new SetTitlePacket();
		$pk->type = SetTitlePacket::TITLE_TYPE_ACTION_BAR;
		$pk->title = $title;
		$pk->fadeInDuration = $fadein;
		$pk->fadeOutDuration = $fadeout;
		$pk->duration = $duration;
		$this->dataPacket($pk);
	}
	
	public function close($message = "", $reason = "Generik Neden"){
		$this->server->saveEverything();
        Win10InvLogic::removeData($this);
        foreach($this->tasks as $t){
			$t->cancel();
		}
		
		$this->tasks = [];
		if($this->connected && !$this->closed){
			$pk = new DisconnectPacket;
			$pk->message = $reason;
			$this->directDataPacket($pk);
			$this->connected = false;
			if($this->username != ""){
				$this->server->getPluginManager()->callEvent($ev = new PlayerQuitEvent($this, $message, $reason));
				if($this->server->getSavePlayerData() && $this->loggedIn === true){
					$this->save();
				}
				//if(!$ev->isCancelled()){
					foreach($this->server->getOnlinePlayers() as $p){
						$p->sendMessage(TF::RED . $this->username . " Oyundan Ayrıldı!");
					//}
				}
			}
			
			foreach($this->server->getOnlinePlayers() as $p){
				if(!$p->canSee($this)){
					$p->showPlayer($this);
				}
				
				$p->despawnFrom($this);
			}
			
			$this->hiddenPlayers = [];
			$this->hiddenEntity = [];
			
			if(!is_null($this->currentWindow)){
				$this->removeWindow($this->currentWindow);
			}

			$this->interface->close($this, $reason);

			$chunkX = $chunkZ = null;
			foreach($this->usedChunks as $index => $d){
				Level::getXZ($index, $chunkX, $chunkZ);
				$this->level->freeChunk($chunkX, $chunkZ, $this);
				unset($this->usedChunks[$index]);
			}

			parent::close();

			$this->server->removeOnlinePlayer($this);

			$this->loggedIn = false;

			$this->server->getPluginManager()->unsubscribeFromPermission(Server::BROADCAST_CHANNEL_USERS, $this);
			$this->spawned = false;
			$this->server->getLogger()->info($this->server->getLanguage()->translateString("pocketmine.player.logOut", [
				TF::AQUA . $this->getName() . TF::WHITE,
				$this->ip,
				$this->port,
				$this->server->getLanguage()->translateString($reason)
			]));
			
			$this->usedChunks = [];
			$this->loadQueue = [];
			$this->hasSpawned = [];
			$this->spawnPosition = null;
			unset($this->buffer);
		}
			
		$this->perm->clearPermissions();
		$this->server->removePlayer($this);
	}
	
	public function save(){
		if($this->closed){
			throw new \InvalidStateException("Tried to save closed player");
		}

		parent::saveNBT();
		if($this->level instanceof Level){
			$this->namedtag->Level = new StringTag("Level", $this->level->getName());
			if($this->spawnPosition instanceof Position && $this->spawnPosition->getLevel() instanceof Level){
				$this->namedtag["SpawnLevel"] = $this->spawnPosition->getLevel()->getName();
				$this->namedtag["SpawnX"] = (int) $this->spawnPosition->x;
				$this->namedtag["SpawnY"] = (int) $this->spawnPosition->y + 0.1;
				$this->namedtag["SpawnZ"] = (int) $this->spawnPosition->z;
			}

			$this->namedtag["playerGameType"] = $this->gamemode;
			$this->namedtag["lastPlayed"] = floor(microtime(true) * 1000);

			if($this->username != "" && $this->namedtag instanceof Compound){
				$this->server->saveOfflinePlayerData($this->username, $this->namedtag, true);
			}
		}
	}
	
	public function getName(){
		return $this->username;
	}
    
    public function getXBLName(){
        return $this->xblName;
    }
	
	public function freeChunks(){
		$x = $z = null;
		foreach($this->usedChunks as $index => $chunk){
			Level::getXZ($index, $x, $z);
			$this->level->freeChunk($x, $z, $this);
			unset($this->usedChunks[$index]);
			unset($this->loadQueue[$index]);
		}
	}

	public function kill(){
		if($this->dead === true || $this->spawned === false){
			return;
		}

		$message = "death.attack.generic";

		$params = [
			$this->getName()
		];

		$cause = $this->getLastDamageCause();

		switch($cause === null ? EntityDamageEvent::CAUSE_CUSTOM : $cause->getCause()){
			case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
				if($cause instanceof EntityDamageByEntityEvent){
					$e = $cause->getDamager();
					if($e instanceof Player){
						$message = "death.attack.player";
						$params[] = $e->getDisplayName();
						break;
					}elseif($e instanceof Living){
						$message = "death.attack.mob";
						$params[] = $e->getNameTag() !== "" ? $e->getNameTag() : $e->getName();
						break;
					}else{
						$params[] = "Bilinmeyen";
					}
				}
				break;
			case EntityDamageEvent::CAUSE_PROJECTILE:
				if($cause instanceof EntityDamageByEntityEvent){
					$e = $cause->getDamager();
					if($e instanceof Player){
						$message = "death.attack.arrow";
						$params[] = $e->getDisplayName();
					}elseif($e instanceof Living){
						$message = "death.attack.arrow";
						$params[] = $e->getNameTag() !== "" ? $e->getNameTag() : $e->getName();
						break;
					}else{
						$params[] = "Bilinmeyen";
					}
				}
				break;
			case EntityDamageEvent::CAUSE_SUICIDE:
				$message = "death.attack.generic";
				break;
			case EntityDamageEvent::CAUSE_VOID:
				$message = "death.attack.outOfWorld";
				break;
			case EntityDamageEvent::CAUSE_FALL:
				if($cause instanceof EntityDamageEvent){
					if($cause->getFinalDamage() > 2){
						$message = "death.fell.accident.generic";
						break;
					}
				}
				$message = "death.attack.fall";
				break;

			case EntityDamageEvent::CAUSE_SUFFOCATION:
				$message = "death.attack.inWall";
				break;

			case EntityDamageEvent::CAUSE_LAVA:
				$message = "death.attack.lava";
				break;

			case EntityDamageEvent::CAUSE_FIRE:
				$message = "death.attack.onFire";
				break;

			case EntityDamageEvent::CAUSE_FIRE_TICK:
				$message = "death.attack.inFire";
				break;

			case EntityDamageEvent::CAUSE_DROWNING:
				$message = "death.attack.drown";
				break;

			case EntityDamageEvent::CAUSE_CONTACT:
				if($cause instanceof EntityDamageByBlockEvent){
					if($cause->getDamager()->getId() === Block::CACTUS){
						$message = "death.attack.cactus";
					}
				}
				break;

			case EntityDamageEvent::CAUSE_BLOCK_EXPLOSION:
			case EntityDamageEvent::CAUSE_ENTITY_EXPLOSION:
				if($cause instanceof EntityDamageByEntityEvent){
					$e = $cause->getDamager();
					if($e instanceof Player){
						$message = "death.attack.explosion.player";
						$params[] = $e->getDisplayName();
					}elseif($e instanceof Living){
						$message = "death.attack.explosion.player";
						$params[] = $e->getNameTag() !== "" ? $e->getNameTag() : $e->getName();
						break;
					}
				}else{
					$message = "death.attack.explosion";
				}
				break;

			case EntityDamageEvent::CAUSE_MAGIC:
				$message = "death.attack.magic";
				break;

			case EntityDamageEvent::CAUSE_CUSTOM:
				break;
				
			default:
		}

		Entity::kill();

		$this->server->getPluginManager()->callEvent($ev = new PlayerDeathEvent($this, $this->getDrops(), $message));
		
		$this->freeChunks();
		
		if(!$ev->getKeepInventory()){
		//if($this->server->getSoftConfig("inventory.keep") === true){
			foreach($ev->getDrops() as $item){
				$this->level->dropItem($this, $item);
			}

			if($this->inventory !== null){
				$this->inventory->clearAll();
			}
		}

		if($ev->getDeathMessage() != ""){
			$this->server->broadcast($ev->getDeathMessage(), Server::BROADCAST_CHANNEL_USERS);
		}

		if($this->server->isHardcore()){
			$this->setBanned(true);
		}else{
			$pk = new RespawnPacket();
			$pos = $this->getSpawn();
			$pk->x = $pos->x;
			$pk->y = $pos->y + 0.1;
			$pk->z = $pos->z;
			$this->dataPacket($pk);
			$this->setMayMove(false);
		}
	}

	public function setHealth($amount){
		parent::setHealth($amount);
		if($this->spawned === true){
			$pk = new UpdateAttributesPacket();
			$pk->entityId = $this->id;
			$this->foodTick = 0;
			$pk->minValue = 0;
			$pk->maxValue = $this->getMaxHealth();
			$pk->value = $this->getHealth();
			$pk->defaultValue = $pk->maxValue;
			$pk->name = UpdateAttributesPacket::HEALTH;
			$this->dataPacket($pk);
		}
	}

	private $hunger = 20;

	protected $hungerDepletion = 0;

	protected $hungerEnabled = true;

	public function setFoodEnabled($enabled){
		$this->hungerEnabled = $enabled;
	}

	public function getFoodEnabled(){
		return $this->hungerEnabled;
	}

	public function setFood($amount){
		if($this->spawned === true){			
			$pk = new UpdateAttributesPacket();
			$pk->entityId = $this->id;
			$pk->minValue = 0;
			$pk->maxValue = 20;
			$pk->value = $amount;
			$pk->defaultValue = $pk->maxValue;
			$pk->name = UpdateAttributesPacket::HUNGER;
			$this->dataPacket($pk);
		}
		
		$this->hunger = $amount;
	}

	public function getFood(){
		return $this->hunger;
	}

	public function subtractFood($amount){
		if(!$this->getFoodEnabled()){
			return false;
		}
		
		if($this->hunger - $amount < 0) return;
		$this->setFood($this->getFood() - $amount);
	}

	public function attack($damage, EntityDamageEvent $source){
		if($this->dead === true){
			return;
		}

		if($this->isCreative()
			and $source->getCause() !== EntityDamageEvent::CAUSE_MAGIC
			and $source->getCause() !== EntityDamageEvent::CAUSE_SUICIDE
			and $source->getCause() !== EntityDamageEvent::CAUSE_VOID
		){
			$source->setCancelled();
		}

		parent::attack($damage, $source);

		if($source->isCancelled()){
			return;
		}elseif($this->getLastDamageCause() === $source && $this->spawned){
			$pk = new EntityEventPacket();
			$pk->eid = $this->id;
			$pk->event = EntityEventPacket::HURT_ANIMATION;
			$this->dataPacket($pk);
		}
	}

	public function sendPosition(Vector3 $pos, $yaw = null, $pitch = null, $mode = MovePlayerPacket::MODE_RESET, array $targets = null){
		$yaw = $yaw === null ? $this->yaw : $yaw;
		$pitch = $pitch === null ? $this->pitch : $pitch;

		$pk = new MovePlayerPacket();
		$pk->eid = $this->getId();
		$pk->x = $pos->x;
		$pk->y = $pos->y + $this->getEyeHeight();
		$pk->z = $pos->z;
		$pk->bodyYaw = $yaw;
		$pk->pitch = $pitch;
		$pk->yaw = $yaw;
		$pk->mode = $mode;

		if($targets !== null){
			Server::broadcastPacket($targets, $pk);
		}else{
			$this->dataPacket($pk);
		}
	}

	protected function checkChunks(){
		$chunkX = $this->x >> 4;
		$chunkZ = $this->z >> 4;
		if($this->chunk === null || $this->chunk->getX() !== $chunkX || $this->chunk->getZ() !== $chunkZ){
			if($this->chunk !== null){
				$this->chunk->removeEntity($this);
			}
			
			$this->chunk = $this->level->getChunk($chunkX, $chunkZ);
			if($this->chunk !== null){
				$this->chunk->addEntity($this);
			}
		}

		$chunkViewers = $this->level->getUsingChunk($this->x >> 4, $this->z >> 4);
		unset($chunkViewers[$this->getId()]);
		foreach($this->hasSpawned as $player){
			if(!isset($chunkViewers[$player->getId()])){
				$this->despawnFrom($player);
			}else{
				unset($chunkViewers[$player->getId()]);
			}
		}

		foreach($chunkViewers as $player){
			$this->spawnTo($player);
		}
	}
	
	public function teleport(Vector3 $pos, $yaw = null, $pitch = null){
		if(!$this->isOnline()){
			return;
		}

		$oldPos = $this->getPosition();
		if(parent::teleport($pos, $yaw, $pitch)){
			if(!is_null($this->currentWindow)){
				$this->removeWindow($this->currentWindow);
			}
			
			$this->forceMovement = new Vector3($this->x, $this->y, $this->z);
			$this->sendPosition($this, $this->pitch, $this->yaw, MovePlayerPacket::MODE_RESET);
			//$this->level->updateAround($pos);
			//$this->level->updateAllLight($pos);
			//$this->level->sendBlocks([$this], [$pos], UpdateBlockPacket::FLAG_ALL_PRIORITY);
			$this->resetFallDistance();
			$this->nextChunkOrderRun = 0;
			$this->newPosition = null;
			$this->lastTeleportTime = microtime(true);
			$this->isTeleportedForMoveEvent = true;
		}
	}
	
	/**
	 * @param Inventory $inventory
	 *
	 * @return int
	 */
	public function getWindowId(Inventory $inventory){
		if($inventory === $this->currentWindow){
			return $this->currentWindowId;
		}elseif($inventory === $this->inventory){
			return 0;
		}
		
		return -1;
	}
	
	public function getCurrentWindowId(){
		return $this->currentWindowId;
	}
	
	public function getCurrentWindow(){
		return $this->currentWindow;
	}

	/**
	 * @param Inventory $inventory
	 * @param int       $forceId
	 *
	 * @return int
	 */
	public function addWindow(Inventory $inventory, $forceId = null){
		if($this->currentWindow === $inventory){
			return $this->currentWindowId;
		}
		if(!is_null($this->currentWindow)){
			$this->removeWindow($this->currentWindow);
		}
		$this->currentWindow = $inventory;
		$this->currentWindowId = !is_null($forceId) ? $forceId : rand(Player::MIN_WINDOW_ID, 98);
		if(!$inventory->open($this)){
			$this->removeWindow($inventory);
		}
		return $this->currentWindowId;
	}

	public function removeWindow(Inventory $inventory){
		if($this->currentWindow !== $inventory){
		}else{
			$inventory->close($this);
			$this->currentWindow = null;
			$this->currentWindowId = -1;
		}
	}

	public function setMetadata($metadataKey, MetadataValue $metadataValue){
		$this->server->getPlayerMetadata()->setMetadata($this, $metadataKey, $metadataValue);
	}

	public function getMetadata($metadataKey){
		return $this->server->getPlayerMetadata()->getMetadata($this, $metadataKey);
	}

	public function hasMetadata($metadataKey){
		return $this->server->getPlayerMetadata()->hasMetadata($this, $metadataKey);
	}

	public function removeMetadata($metadataKey, Plugin $plugin){
		$this->server->getPlayerMetadata()->removeMetadata($this, $metadataKey, $plugin);
	}
	
	public function setLastMessageFrom($name){
		$this->lastMessageReceivedFrom = (string)$name;
	}

	public function getLastMessageFrom(){
		return $this->lastMessageReceivedFrom;
	}
	
	public function setIdentifier($identifier){
		$this->identifier = $identifier;
	}
	
	public function getIdentifier(){
		return $this->identifier;
	}	
	
	public function getVisibleEyeHeight(){
		return $this->eyeHeight;
	}
	
	public function kickOnFullServer(){
		return true;
	}
	
	public function processLogin(){
		$pk = new PlayStatusPacket();
		$pk->status = PlayStatusPacket::LOGIN_SUCCESS;
		$this->dataPacket($pk);
		
		$pk = new ResourcePackInfoPacket();
		$this->dataPacket($pk);
		
		//$pk = new BehaviorPackInfoPacket();
		//$this->dataPacket($pk);
	}
	
	public function completeLogin(){
		$this->server->saveEverything();
		$valid = true;
		$len = strlen($this->username);
		if($len > 16 || $len < 3){
			$valid = false;
		}
		for($i = 0; $i < $len && $valid; ++$i){
			$c = ord($this->username{$i});
			if(($c >= ord("a") && $c <= ord("z")) || ($c >= ord("A") && $c <= ord("Z")) || ($c >= ord("0") && $c <= ord("9")) || $c === ord("_") || $c === ord(" ")){
				continue;
			}
			$valid = false;
			break;
		}
		if(!$valid || $this->iusername === "rcon" || $this->iusername === "console" || $this->iusername === "sunucu" || $this->iusername === "konsol" || $this->iusername === "darkbot" || $this->iusername === "dark bot" || $this->iusername === "dbot" || $this->iusername === "d bot"){
			$this->close("", "disconnectionScreen.invalidName", false);
			return;
		}
		$leet = "leet";
		$playmc = "playmc";
		//$usrname = strtolower($this->iusername);
		$usrname = $this->iusername;
		if(strpos($usrname, $leet) || strpos($usrname, $playmc)){
			$this->close("", "disconnectionScreen.noAdvertising", false);
			return;
		}
		if(strlen($this->skin) !== 64 * 32 * 4 && strlen($this->skin) !== 64 * 64 * 4){
			$this->close("", "disconnectionScreen.invalidSkin", false);
			return;
		}
		if(count($this->server->getOnlinePlayers()) >= $this->server->getMaxPlayers() && $this->kickOnFullServer()){
			$this->close("", "disconnectionScreen.serverFull", false);
			return;
		}
		$this->server->getPluginManager()->callEvent($ev = new PlayerPreLoginEvent($this, "Plugin reason"));
		if($ev->isCancelled()){
			$this->close("", $ev->getKickMessage());
			return;
		}
		if(!$this->server->isWhitelisted(strtolower($this->getName()))){
			$this->close(TF::YELLOW . $this->username . " Oyundan Ayrıldı", "Sunucu Beyaz-Liste'de.");
			return;
		}elseif($this->server->getNameBans()->isBanned(strtolower($this->getName())) || $this->server->getIPBans()->isBanned($this->getAddress())){
			$this->close(TF::YELLOW . $this->username . " Oyundan Ayrıldı", "Sunucuya Girmeniz Yasak.");
			return;
		}
		if($this->hasPermission(Server::BROADCAST_CHANNEL_USERS)){
			$this->server->getPluginManager()->subscribeToPermission(Server::BROADCAST_CHANNEL_USERS, $this);
		}
		if($this->hasPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE)){
			$this->server->getPluginManager()->subscribeToPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE, $this);
		}
		foreach($this->server->getOnlinePlayers() as $p){
			if($p !== $this && strtolower($p->getName()) === strtolower($this->getName())){
				if($this->xuid !== ''){
					$p->close(TF::YELLOW . $p->getName() . " has left the game", "You connected from somewhere else.");
				}elseif($p->kick("You connected from somewhere else.") === false){
					$this->close(TF::YELLOW . $this->getName() . " has left the game", "You connected from somewhere else.");
					return;
				}
			}
		}
		$nbt = $this->server->getOfflinePlayerData($this->username);
		if(!isset($nbt->NameTag)){
			$nbt->NameTag = new StringTag("NameTag", $this->username);
		}else{
			$nbt["NameTag"] = $this->username;
		}
		$this->gamemode = $nbt["playerGameType"] & 0x03;
		if($this->server->getForceGamemode()){
			$this->gamemode = $this->server->getGamemode();
			$nbt->playerGameType = new IntTag("playerGameType", $this->gamemode);
		}
		$this->allowFlight = $this->isCreative();
		if(($level = $this->server->getLevelByName($nbt["Level"])) === null){
			$this->setLevel($this->server->getDefaultLevel(), true);
			$nbt["Level"] = $this->level->getName();
			$nbt["Pos"][0] = $this->level->getSpawnLocation()->x;
			$nbt["Pos"][1] = $this->level->getSpawnLocation()->y + 0.1;
			$nbt["Pos"][2] = $this->level->getSpawnLocation()->z;
		}else{
			$this->setLevel($level, true);
		}
		if(!$nbt instanceof Compound){
			$this->close(TF::YELLOW . $this->username . " has left the game", "Corrupt joining data, check your connection.");
			return;
		}
		$this->achievements = [];
		foreach($nbt->Achievements as $achievement){
			$this->achievements[$achievement->getName()] = $achievement->getValue() > 0 ? true : false;
		}
		$nbt->lastPlayed = new LongTag("lastPlayed", floor(microtime(true) * 1000));
		parent::__construct($this->level, $nbt);
		$this->server->addOnlinePlayer($this);
		if($this->isCreative()){
			$this->inventory->setHeldItemSlot(0);
		}else{
			$this->inventory->setHeldItemSlot($this->inventory->getHotbarSlotIndex(0));
		}
		if($this->spawnPosition === null && isset($this->namedtag->SpawnLevel) && ($level = $this->server->getLevelByName($this->namedtag["SpawnLevel"])) instanceof Level){
			$this->spawnPosition = new Position($this->namedtag["SpawnX"], $this->namedtag["SpawnY"], $this->namedtag["SpawnZ"], $level);
		}
		$spawnPosition = $this->getSpawn();
		$hub = $this->server->getDefaultLevel()->getSafeSpawn();
		//$lobby = $this->level->getSafeSpawn();
		//$this->level->updateAround($lobby);
		//$this->level->updateAllLight($lobby);
		//$this->level->sendBlocks([$this], [$lobby], UpdateBlockPacket::FLAG_ALL_PRIORITY);
		$pk = new StartGamePacket();
		$pk->seed = -1;
		$pk->dimension = 0;
		$pk->x = $this->x;
		$pk->y = $this->y + 0.1;
		$pk->z = $this->z;
		$pk->spawnX = $hub->x;
		$pk->spawnY = $hub->y + 0.1;
		$pk->spawnZ = $hub->z;
		$pk->generator = 1;
		$pk->gamemode = $this->gamemode & 0x01;
		$pk->eid = $this->id;
		$this->dataPacket($pk);
		$pk = new SetTimePacket();
		$pk->time = $this->level->getTime();
		$pk->started = true;
		$this->dataPacket($pk);
		$pk = new SetSpawnPositionPacket();
		$pk->x = (int) $spawnPosition->x;
		$pk->y = (int) $spawnPosition->y + 0.1;
		$pk->z = (int) $spawnPosition->z;
		$this->dataPacket($pk);
		if($this->getHealth() <= 0){
			$this->dead = true;
		}
		//$pk = new ResourcePackDataInfoPacket();
		//$this->dataPacket($pk);
		//$pk = new BehaviorPackDataInfoPacket();
		//$this->dataPacket($pk);
		//$pk = new SetCommandsEnabledPacket();
		//$pk->enabled = 1;
		//$this->dataPacket($pk);
		if(!empty(Player::$availableCommands)){
			$pk = new AvailableCommandsPacket();
			$this->dataPacket($pk);
		}
		if($this->getHealth() <= 0){
			$this->dead = true;
		}
		$pk = new SetDifficultyPacket();
		$pk->difficulty = $this->server->getDifficulty();
		$this->dataPacket($pk);
		$this->sendAttributes(true);
		$this->setNameTagVisible(true);
		$this->setNameTagAlwaysVisible(true);
		$this->server->getLogger()->info($this->server->getLanguage()->translateString("pocketmine.player.logIn", [
			TF::AQUA . $this->username . TF::WHITE,
			$this->ip,
			$this->port,
			TF::GREEN . $this->randomClientId . TF::WHITE,
			$this->id,
			$this->level->getName(),
			round($this->x, 4),
			round($this->y, 4),
			round($this->z, 4)
		]));
		$slots = [];
		foreach(Item::getCreativeItems() as $item){
			$slots[] = clone $item;
		}
		Multiversion::sendContainer($this, Protocol120::CONTAINER_ID_CREATIVE, $slots);
		$this->server->sendRecipeList($this);
		$this->sendCommandData();
		$this->sendSelfData();
		$this->updateSpeed(Player::DEFAULT_SPEED);
		$this->setMayMove(false);
	}
	
	public function getInterface(){
		return $this->interface;
	}
	
	public function getItemInHand(){
		return $this->inventory->getItemInHand();
	}
	
	public function chatPlayer($format){
		foreach($this->server->getOnlinePlayers() as $p){
			$p->sendMessage($format);
		}
	}
	
	public function sendFace(){
		$this->server->getScheduler()->scheduleAsyncTask(new SendPlayerFaceTask($this->getSkinData()));
	}
	
	public function givePizza($mark){
		if(!$this instanceof Player){
			return;
		}
		
		$item = Item::get(Item::COOKIE, 0, 1);
		$pizza = $item->setCustomName(TF::AQUA . $mark . " §6Pizzası");
		$this->inventory->addItem(clone $pizza);
	}
	
	public function transfer($address, $port = false){
		$pk = new TransferPacket();
		$pk->ip = $address;
		$pk->port = ($port === false ? 19132 : $port);
		$this->dataPacket($pk);
	}
	
	public function sendSelfData(){
		$pk = new SetEntityDataPacket();
		$pk->eid = $this->id;
		$pk->metadata = $this->dataProperties;
		$this->dataPacket($pk);
	}
	
	protected function addTransaction($transaction){
		$newItem = $transaction->getTargetItem();
		$oldItem = $transaction->getSourceItem();
		if($newItem->getId() === Item::AIR || ($oldItem->deepEquals($newItem) && $oldItem->count > $newItem->count)){
			return;
		}
		
		$inventory = $this->currentWindow;
		if(is_null($this->currentWindow) || $this->currentWindow === $transaction->getInventory()){
			$inventory = $this->inventory;
		}
		
		if($oldItem->deepEquals($newItem)){
			$newItem->count -= $oldItem->count;
		}

		$items = $inventory->getContents();
		$targetSlot = -1;
		foreach($items as $slot => $item){
			if($item->deepEquals($newItem) && $newItem->count <= $item->count){
				$targetSlot = $slot;
				break;
			}
		}
		
		if($targetSlot !== -1){
			$trGroup = new SimpleTransactionGroup($this);
			$trGroup->addTransaction($transaction);
			if(!$oldItem->deepEquals($newItem) && $oldItem->getId() !== Item::AIR && $inventory === $transaction->getInventory()){ // for swap
				$targetItem = clone $oldItem;
			}elseif($newItem->count === $items[$targetSlot]->count){
				$targetItem = Item::get(Item::AIR);
			}else{
				$targetItem = clone $items[$targetSlot];
				$targetItem->count -= $newItem->count;
			}
			
			$pairTransaction = new BaseTransaction($inventory, $targetSlot, $items[$targetSlot], $targetItem);
			$trGroup->addTransaction($pairTransaction);
			
			try {
				$isExecute = $trGroup->execute();
				if(!$isExecute){
					$trGroup->sendInventories();
				}
			} catch (\Exception $ex){
				$trGroup->sendInventories();
			}
		}else{
			$transaction->getInventory()->sendContents($this);
		}
	}
	
	protected function enchantTransaction(BaseTransaction $transaction){
		if($this->craftingType !== Player::CRAFTING_ENCHANT){
			$this->inventory->sendContents($this);
			return;
		}
		
		$oldItem = $transaction->getSourceItem();
		$newItem = $transaction->getTargetItem();
		$enchantInv = $this->currentWindow;
		if(($newItem instanceof Armor || $newItem instanceof Tool) && $transaction->getInventory() === $this->inventory){
			$source = $enchantInv->getItem(0);
			$enchantingLevel = $enchantInv->getEnchantingLevel();
			if($enchantInv->isItemWasEnchant() && $newItem->deepEquals($source, true, false)){
				$enchantInv->setItem(0, Item::get(Item::AIR));
				$enchantInv->setEnchantingLevel(0);
				$playerItems = $this->inventory->getContents();
				$dyeSlot = -1;
				$targetItemSlot = -1;
				foreach($playerItems as $slot => $item){
					if($item->getId() === Item::DYE && $item->getDamage() === 4 && $item->getCount() >= $enchantingLevel){
						$dyeSlot = $slot;
					}elseif($item->deepEquals($source)){
						$targetItemSlot = $slot;
					}
				}
				
				if($dyeSlot !== -1 && $targetItemSlot !== -1){
					$this->inventory->setItem($targetItemSlot, $newItem);
					if($playerItems[$dyeSlot]->getCount() > $enchantingLevel){
						$playerItems[$dyeSlot]->count -= $enchantingLevel;
						$this->inventory->setItem($dyeSlot, $playerItems[$dyeSlot]);
					}else{
						$this->inventory->setItem($dyeSlot, Item::get(Item::AIR));
					}
				}
			}elseif(!$enchantInv->isItemWasEnchant()){
				$enchantInv->setItem(0, Item::get(Item::AIR));
			}
			
			$enchantInv->sendContents($this);
			$this->inventory->sendContents($this);
			return;
		}
		
		if(($oldItem instanceof Armor || $oldItem instanceof Tool) && $transaction->getInventory() === $this->inventory){
			$enchantInv->setItem(0, $oldItem);
		}
	}
	
	protected function updateAttribute($name, $value, $minValue, $maxValue, $defaultValue){
		$pk = new UpdateAttributesPacket();
		$pk->entityId = $this->id;
		$pk->name = $name;
		$pk->value = $value;
		$pk->minValue = $minValue;
		$pk->maxValue = $maxValue;
		$pk->defaultValue = $defaultValue;
		$this->dataPacket($pk);
	}
	
	public function updateSpeed($value){
		$this->movementSpeed = $value;
		$this->updateAttribute(UpdateAttributesPacket::SPEED, $this->movementSpeed, 0, Player::MAXIMUM_SPEED, $this->movementSpeed);
	}

	public function setSprinting($value = true, $setDefault = false){
		if(!$setDefault && $this->isSprinting() == $value){
			return;
		}
		
		parent::setSprinting($value);
		if($setDefault){
			$this->movementSpeed = Player::DEFAULT_SPEED;
		}else{
			$sprintSpeedChange = Player::DEFAULT_SPEED * 0.3;
			if($value === false){
				$sprintSpeedChange *= -1;
			}
			
			$this->movementSpeed += $sprintSpeedChange;
		}
		
		$this->updateSpeed($this->movementSpeed);
	}

	public function checkVersion(){
		if(!$this->loggedIn){
			$this->close("", TF::RED . "Oyun Sürümünüz Uyumlu Değil!");
		}else{
			var_dump('zlib_decode Hatası');
		}
	}
	
	public function getProtectionEnchantments(){
		$result = [
			Enchantment::TYPE_ARMOR_PROTECTION => null,
			Enchantment::TYPE_ARMOR_FIRE_PROTECTION => null,
			Enchantment::TYPE_ARMOR_EXPLOSION_PROTECTION => null,
			Enchantment::TYPE_ARMOR_FALL_PROTECTION => null,
			Enchantment::TYPE_ARMOR_PROJECTILE_PROTECTION => null
		];
		
		$armor = $this->inventory->getArmorContents();
		foreach($armor as $item){
			if($item->getId() === Item::AIR){
				continue;
			}
			
			$enchantments = $item->getEnchantments();
			foreach($result as $id => $enchantment){
				if(isset($enchantments[$id]) && (is_null($enchantment) || $enchantments[$id]->getLevel() > $enchantment->getLevel())){
					$result[$id] = $enchantments[$id];
				}
			}
		}
		
		return $result;
	}
	
	public function getExp()
	{
		return $this->exp;
	}
	
	public function getExperience()
	{
		return $this->exp;
	}
	
	public function getExpLevel()
	{
		return $this->expLevel;
	}
	
	public function getExperienceLevel()
	{
		return $this->expLevel;
	}
	
	public function updateExperience($exp = 0, $level = 0, $checkNextLevel = true)
	{
		$this->exp = $exp;
		$this->expLevel = $level;

		$this->updateAttribute(UpdateAttributesPacket::EXPERIENCE, $exp, 0, Player::MAX_EXPERIENCE, 100);
		$this->updateAttribute(UpdateAttributesPacket::EXPERIENCE_LEVEL, $level, 0, Player::MAX_EXPERIENCE_LEVEL, 100);

		if($this->hasEnoughExperience() && $checkNextLevel){
			$exp = 0;
			$level = $this->getExperienceLevel() + 1;
			$this->updateExperience($exp, $level, false);
		}
	}
	
	public function addExp($exp = 0, $level = 0, $checkNextLevel = true)
	{
		$this->updateExperience($this->getExperience() + $exp, $this->getExperienceLevel() + $level, $checkNextLevel);
	}
	
	public function addExperience($exp = 0, $level = 0, $checkNextLevel = true)
	{
		$this->updateExperience($this->getExperience() + $exp, $this->getExperienceLevel() + $level, $checkNextLevel);
	}
	
	public function removeExp($exp = 0, $level = 0, $checkNextLevel = true)
	{
		$this->updateExperience($this->getExperience() - $exp, $this->getExperienceLevel() - $level, $checkNextLevel);
	}
	
	public function removeExperience($exp = 0, $level = 0, $checkNextLevel = true)
	{
		$this->updateExperience($this->getExperience() - $exp, $this->getExperienceLevel() - $level, $checkNextLevel);
	}
	
	public function getExperienceNeeded()
	{
		$level = $this->getExperienceLevel();
		if($level <= 16){
			return (2 * $level) + 7;
		}elseif($level <= 31){
			return (5 * $level) - 38;
		}elseif($level <= 21863){
			return (9 * $level) - 158;
		}
		
		return PHP_INT_MAX;
	}

	public function hasEnoughExperience()
	{
		return $this->getExperienceNeeded() - $this->getRealExperience() <= 0;
	}

	public function getRealExperience(){
		return $this->getExperienceNeeded() * $this->getExperience();
	}
	
	public function isUseElytra(){
		return ($this->isHaveElytra() && $this->elytraIsActivated);
	}
	
	public function isHaveElytra(){
		if($this->inventory->getArmorItem(Elytra::SLOT_NUMBER) instanceof Elytra){
			return true;
		}
		
		return false;
	}

	public function setElytraActivated($value){
		$this->elytraIsActivated = $value;
	}

	public function isElytraActivated(){
		return $this->elytraIsActivated;
	}
	
	public function getPlayerProtocol(){
		return $this->protocol;
	}

	public function getDeviceOS(){
        return $this->deviceType;
    }
    
    public function getInventoryType(){
        return $this->inventoryType;
    }
	
	public function setPing($ping){
		$this->ping = $ping;
	}
	
	public function getPing(){
		return $this->ping;
	}
	
	public function sendPing(){
		if($this->ping <= 150){
			$this->sendMessage(TF::GREEN . "Bağlantı: İyi ({$this->ping}ms)");
		}elseif($this->ping <= 250){
			$this->sendMessage(TF::YELLOW . "Bağlantı: Orta ({$this->ping}ms)");
		}else{
			$this->sendMessage(TF::RED . "Bağlantı: Kötü ({$this->ping}ms)");
		}
	}
    
    public function getXUID(){
        return $this->xuid;
    }
	
	public function setTitle($text, $subtext = '', $time = 36000){
		if($this->protocol >= ProtocolInfo::PROTOCOL_105){		
			$pk = new SetTitlePacket();
			$pk->type = SetTitlePacket::TITLE_TYPE_TIMES;
			$pk->text = "";
			$pk->fadeInTime = 5;
			$pk->fadeOutTime = 5;
			$pk->stayTime = 20 * $time;
			$this->dataPacket($pk);
			if(!empty($subtext)){
				$pk = new SetTitlePacket();
				$pk->type = SetTitlePacket::TITLE_TYPE_SUBTITLE;
				$pk->text = $subtext;
				$this->dataPacket($pk);
			}
			
			$pk = new SetTitlePacket();
			$pk->type = SetTitlePacket::TITLE_TYPE_TITLE;
			$pk->text = $text;
			$this->dataPacket($pk);	
		}
	}

	public function clearTitle(){
		if($this->protocol >= ProtocolInfo::PROTOCOL_105){
			$pk = new SetTitlePacket();
			$pk->type = SetTitlePacket::TITLE_TYPE_CLEAR;
			$pk->text = "";
			$this->dataPacket($pk);
		}
	}
		
	public function sendNoteSound($noteId, $queue = false){
		if($queue){
			$this->noteSoundQueue[] = $noteId;
			return;
		}
		
		$pk = new LevelSoundEventPacket();
		$pk->eventId = LevelSoundEventPacket::SOUND_NOTE;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->entityType = $noteId;
		$this->directDataPacket($pk);
	}
		
	public function canSeeEntity(Entity $entity){
		return !isset($this->hiddenEntity[$entity->getId()]);
	}

	public function hideEntity(Entity $entity){
		if($entity instanceof Player){
			return;
		}
		
		$this->hiddenEntity[$entity->getId()] = $entity;
		$entity->despawnFrom($this);
	}

	public function showEntity(Entity $entity){
		if($entity instanceof Player){
			return;
		}
		
		unset($this->hiddenEntity[$entity->getId()]);
		if($entity !== $this && !$entity->closed && !$entity->dead){
			$entity->spawnTo($this);
		}
	}
	
	public function setOnFire($seconds, $damage = 1){
 		if($this->isSpectator()){
 			return;
 		}
 
 		parent::setOnFire($seconds, $damage);
 	}
	
	public function attackByTargetId($targetId){
		if($this->spawned === false || $this->dead === true || $this->blocked){
			return;
		}

		$target = $this->level->getEntity($targetId);
		if($target instanceof Player && ($this->server->getConfigBoolean("pvp", true) === false || ($target->getGamemode() & 0x01) > 0)){
			return;
		}

		if(!$target instanceof Entity || $this->isSpectator() && !$this->isCreative() && !$this->isSurvival() && !$this->isAdventure() || $target->dead === true){
			return;
		}

		if($target instanceof DroppedItem || $target instanceof Arrow){
			$this->kick("Attempting to attack an invalid entity");
			return;
		}

		$item = $this->inventory->getItemInHand();
		$damageTable = [
			Item::WOODEN_SWORD => 4,
			Item::GOLD_SWORD => 4,
			Item::STONE_SWORD => 5,
			Item::IRON_SWORD => 6,
			Item::DIAMOND_SWORD => 7,
			Item::WOODEN_AXE => 3,
			Item::GOLD_AXE => 3,
			Item::STONE_AXE => 3,
			Item::IRON_AXE => 5,
			Item::DIAMOND_AXE => 6,
			Item::WOODEN_PICKAXE => 2,
			Item::GOLD_PICKAXE => 2,
			Item::STONE_PICKAXE => 3,
			Item::IRON_PICKAXE => 4,
			Item::DIAMOND_PICKAXE => 5,
			Item::WOODEN_SHOVEL => 1,
			Item::GOLD_SHOVEL => 1,
			Item::STONE_SHOVEL => 2,
			Item::IRON_SHOVEL => 3,
			Item::DIAMOND_SHOVEL => 4,
		];
		
		$damage = [
			EntityDamageEvent::MODIFIER_BASE => isset($damageTable[$item->getId()]) ? $damageTable[$item->getId()] : 1,
		];

		if($this->distance($target) > 4){
			return;
		}elseif($target instanceof Player){
			$armorValues = [
				Item::LEATHER_CAP => 1,
				Item::LEATHER_TUNIC => 3,
				Item::LEATHER_PANTS => 2,
				Item::LEATHER_BOOTS => 1,
				Item::CHAIN_HELMET => 1,
				Item::CHAIN_CHESTPLATE => 5,
				Item::CHAIN_LEGGINGS => 4,
				Item::CHAIN_BOOTS => 1,
				Item::GOLD_HELMET => 1,
				Item::GOLD_CHESTPLATE => 5,
				Item::GOLD_LEGGINGS => 3,
				Item::GOLD_BOOTS => 1,
				Item::IRON_HELMET => 2,
				Item::IRON_CHESTPLATE => 6,
				Item::IRON_LEGGINGS => 5,
				Item::IRON_BOOTS => 2,
				Item::DIAMOND_HELMET => 3,
				Item::DIAMOND_CHESTPLATE => 8,
				Item::DIAMOND_LEGGINGS => 6,
				Item::DIAMOND_BOOTS => 3,
			];
			
			$points = 0;
			foreach($target->getInventory()->getArmorContents() as $index => $i){
				if(isset($armorValues[$i->getId()])){
					$points += $armorValues[$i->getId()];
				}
			}

			$damage[EntityDamageEvent::MODIFIER_ARMOR] = -floor($damage[EntityDamageEvent::MODIFIER_BASE] * $points * 0.04);
		}

		$timeDiff = microtime(true) - $this->lastDamageTime;
		$this->lastDamageTime = microtime(true);

		foreach(Player::$damageTimeList as $time => $koef){
			if($timeDiff <= $time){
				if($koef == 0){
					return;
				}
				
				$damage[EntityDamageEvent::MODIFIER_BASE] *= $koef;
				break;
			}
		}
		
		$ev = new EntityDamageByEntityEvent($this, $target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $damage);
		$target->attack($ev->getFinalDamage(), $ev);
		$this->level->addSound(new LaunchSound($this), $this->getViewers());
		if($ev->isCancelled()){
			if($item->isTool() && $this->isSurvival() || $this->isAdventure() && !$this->isCreative() && !$this->isSpectator()){
				$this->inventory->sendContents($this);
			}
			
			return;
		}

		if($item->isTool() && $this->isSurvival() || $this->isAdventure() && !$this->isCreative() && !$this->isSpectator()){
			if($item->useOn($target) && $item->getDamage() >= $item->getMaxDurability()){
				$this->inventory->setItemInHand(Item::get(Item::AIR, 0, 1), $this);
			}elseif($this->inventory->getItemInHand()->getId() == $item->getId()){
				$this->inventory->setItemInHand($item, $this);
			}
		}
	}
	
	protected function useItem($item, $slot, $face, $blockPosition, $clickPosition){
		switch($face){
			case 0:
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
				$blockVector = new Vector3($blockPosition['x'], $blockPosition['y'], $blockPosition['z']);
				$this->setDataFlag(Player::DATA_FLAGS, Player::DATA_FLAG_ACTION, false);

				$itemInHand = $this->inventory->getItemInHand();
				if($blockVector->distance($this) > 10 || ($this->isCreative() && $this->isAdventure())){

				}elseif($this->isCreative()/* && !$this->isSpectator() && !$this->isSurvival() && !$this->isAdventure()*/){
					if($this->level->useItemOn($blockVector, $itemInHand, $face, $clickPosition['x'], $clickPosition['y'], $clickPosition['z'], $this) === true){
						return;
					}
				}elseif(!$itemInHand->deepEquals($item)){
				}else{
					$oldItem = clone $itemInHand;
					if($this->level->useItemOn($blockVector, $itemInHand, $face, $clickPosition['x'], $clickPosition['y'], $clickPosition['z'], $this)){
						if(!$itemInHand->deepEquals($oldItem) || $itemInHand->getCount() !== $oldItem->getCount()){
							$this->inventory->setItemInHand($itemInHand, $this);
							$this->inventory->sendHeldItem($this->hasSpawned);
						}
						
						return;
					}
				}

				$this->inventory->sendHeldItem($this);

				if($blockVector->distanceSquared($this) > 10000){
					return;
				}
				
				$target = $this->level->getBlock($blockVector);
				$block = $target->getSide($face);

				$this->level->sendBlocks([$this], [$target, $block], UpdateBlockPacket::FLAG_ALL_PRIORITY);
				return;
			case 0xff:
			case -1:
				if($this->isSpectator() && !$this->isCreative() && !$this->isSurvival() && !$this->isAdventure()){
					$this->inventory->sendHeldItem($this);
					if($this->inventory->getHeldItemSlot() !== -1){
						$this->inventory->sendContents($this);
					}
					
					return;
				}

				$itemInHand = $this->inventory->getItemInHand();
				if(!$itemInHand->deepEquals($item)){
					$this->inventory->sendHeldItem($this);
					//Timings::$timerUseItemPacket->stopTiming();
					return;
				}

				if($blockPosition['x'] != 0 || $blockPosition['y'] != 0 || $blockPosition['z'] != 0){
					$vectorLength = sqrt($blockPosition['x'] ** 2 + $blockPosition['y'] ** 2 + $blockPosition['z'] ** 2);
					$aimPos = new Vector3($blockPosition['x'] / $vectorLength, $blockPosition['y'] / $vectorLength, $blockPosition['z'] / $vectorLength);
				}else{
					$aimPos = new Vector3(0, 0, 0);
				}

				$ev = new PlayerInteractEvent($this, $itemInHand, $aimPos, $face, PlayerInteractEvent::RIGHT_CLICK_AIR);
				$this->server->getPluginManager()->callEvent($ev);
				if($ev->isCancelled()){
					$this->inventory->sendHeldItem($this);
					if($this->inventory->getHeldItemSlot() !== -1){
						$this->inventory->sendContents($this);
					}
					
					return;
				}
				
				if($itemInHand->getId() === Item::SNOWBALL || $itemInHand->getId() === Item::EGG/* || $itemInHand->getId() === Item::BOAT*/){
					$yawRad = $this->yaw / 180 * M_PI;
					$pitchRad = $this->pitch / 180 * M_PI;
					$nbt = new Compound("", [
						"Pos" => new Enum("Pos", [
							new DoubleTag("", $this->x),
							new DoubleTag("", $this->y + $this->getEyeHeight()),
							new DoubleTag("", $this->z)
						]),
						"Motion" => new Enum("Motion", [
							new DoubleTag("", -sin($yawRad) * cos($pitchRad)),
							new DoubleTag("", -sin($pitchRad)),
							new DoubleTag("", cos($yawRad) * cos($pitchRad))
						]),
						"Rotation" => new Enum("Rotation", [
							new FloatTag("", $this->yaw),
							new FloatTag("", $this->pitch)
						]),
					]);

					$f = 1.4; //Default: 1.5
					switch($itemInHand->getId()){
						case Item::SNOWBALL:
							$projectile = Entity::createEntity("Snowball", $this->level, $nbt, $this);
							break;
						case Item::EGG:
							$projectile = Entity::createEntity("Egg", $this->level, $nbt, $this);
							break;
						/*case Item::BOAT:
							$projectile = Entity::createEntity("Boat", $this->level, $nbt, $this);
							break;*/
					}
					
					$projectile->setMotion($projectile->getMotion()->multiply($f));
					if($this->isSurvival() || $this->isAdventure()/* && !$this->isCreative() && !$this->isSpectator()*/){
						$itemInHand->setCount($itemInHand->getCount() - 1);
						$this->inventory->setItemInHand($itemInHand->getCount() > 0 ? $itemInHand : Item::get(Item::AIR));
					}
					
					if($projectile instanceof Projectile){
						$this->server->getPluginManager()->callEvent($projectileEv = new ProjectileLaunchEvent($projectile));
						if($projectileEv->isCancelled()){
							$projectile->kill();
						}else{
							$projectile->spawnToAll();
							$this->level->addSound(new LaunchSound($this), $this->getViewers());
						}
					}else{
						$projectile->spawnToAll();
					}
				}

				$this->setDataFlag(Player::DATA_FLAGS, Player::DATA_FLAG_ACTION, true);
				$this->startAction = $this->server->getTick();
				return;
		}
	}
	
	private function breakBlock($blockPosition){
		if($this->spawned === false || $this->blocked === true || $this->dead === true || $this->isAdventure() || $this->isSpectator()){
			return;
		}
		$vector = new Vector3($blockPosition['x'], $blockPosition['y'], $blockPosition['z']);
		$item = $this->inventory->getItemInHand();
		$oldItem = clone $item;
		if($this->level->useBreakOn($vector, $item, $this) === true){
			if($this->isSurvival()/* && !$this->isAdventure() && !$this->isCreative() && !$this->isSpectator()*/){
				if(!$item->equals($oldItem, true) || $item->getCount() !== $oldItem->getCount()){
					$this->inventory->setItemInHand($item, $this);
					$this->inventory->sendHeldItem($this->hasSpawned);
				}
			}
			return;
		}
		$this->inventory->sendContents($this);
		$target = $this->level->getBlock($vector);
		$tile = $this->level->getTile($vector);
		$this->level->sendBlocks([$this], [$target], UpdateBlockPacket::FLAG_ALL_PRIORITY);
		$this->inventory->sendHeldItem($this);
		if($tile instanceof Spawnable){
			$tile->spawnTo($this);
		}
	}
	
	private function normalTransactionLogic($packet){
		$trGroup = new SimpleTransactionGroup($this);
		foreach($packet->transactions as $trData){
			if($trData->isDropItemTransaction()){
				$this->tryDropItem($packet->transactions);
				return;
			}
			
			if($trData->isCompleteEnchantTransaction()){
				$this->tryEnchant($packet->transactions);
				return;
			}
			
			$transaction = $trData->convertToTransaction($this);
			if($transaction == null){
				$trGroup->sendInventories();
				return;
			}
			
			$trGroup->addTransaction($transaction);
		}
		try{
			if(!$trGroup->execute()){
				$trGroup->sendInventories();
			}else{
			}
		}catch(\Exception $ex){
			$trGroup->sendInventories();
		}
	}
	
	private function tryDropItem($transactionsData){
		$dropItem = null;
		$transaction = null;
		foreach($transactionsData as $trData){
			if($trData->isDropItemTransaction()){
				$dropItem = $trData->newItem;
			}else{
				$transaction = $trData->convertToTransaction($this);
			}
		}
		
		if($dropItem == null || $transaction == null){
			$this->inventory->sendContents($this);
			if($this->currentWindow != null){
				$this->currentWindow->sendContents($this);
			}
			
			return;
		}
		
		$inventory = $transaction->getInventory();
		$item = $inventory->getItem($transaction->getSlot());
		if(!$item->equals($dropItem) || $item->count < $dropItem->count){
			$inventory->sendContents($this);
			return;
		}
		
		$ev = new PlayerDropItemEvent($this, $dropItem);
		$this->server->getPluginManager()->callEvent($ev);
		if($ev->isCancelled()){
			$inventory->sendContents($this);
			return;
		}
		
		if($item->count == $dropItem->count){
			$item = Item::get(Item::AIR, 0, 0);
		}else{
			$item->count -= $dropItem->count;
		}
		
		$inventory->setItem($transaction->getSlot(), $item);
		$motion = $this->getDirectionVector()->multiply(0.4);
		$this->level->dropItem($this->add(0, 1.3, 0), $dropItem, $motion, 40);
		$this->setDataFlag(Player::DATA_FLAGS, Player::DATA_FLAG_ACTION, false);
	}
	
	private static function tryApplyCraft(&$craftSlots, $recipe){
		if($recipe instanceof ShapedRecipe){
			$ingredients = [];
			$itemGrid = $recipe->getIngredientMap();
			foreach($itemGrid as $line){
				foreach($line as $item){
					$ingredients[] = $item;
				}
			}
		}elseif($recipe instanceof ShapelessRecipe){
			$ingredients = $recipe->getIngredientList();
		}
		
		$ingredientsCount = count($ingredients);
		$firstIndex = 0;
		foreach($craftSlots as &$item){
			if($item == null || $item->getId() == Item::AIR){
				continue;
			}
			
			for($i = $firstIndex; $i < $ingredientsCount; $i++){
				$ingredient = $ingredients[$i];
				if($ingredient->getId() == Item::AIR){
					continue;
				}
				
				$isItemsNotEquals = $item->getId() != $ingredient->getId() || 
					($item->getDamage() != $ingredient->getDamage() && $ingredient->getDamage() != 32767) || 
					$item->count < $ingredient->count;
				if($isItemsNotEquals){
					throw new \Exception('Receive bad recipe');
				}
				
				$firstIndex = $i + 1;
				$item->count -= $ingredient->count;
				if($item->count == 0){
					$item = Item::get(Item::AIR, 0, 0);
				}
				
				break;
			}
		}
	}
	
	private function crackBlock($packet){
		if(!isset($this->actionsNum['CRACK_BLOCK'])){
			$this->actionsNum['CRACK_BLOCK'] = 0;
		}
		$recipients = $this->getViewers();
		$recipients[] = $this;
		$blockId = $this->level->getBlockIdAt($packet->x, $packet->y, $packet->z);
		$blockPos = [
			'x' => $packet->x,
			'y' => $packet->y,
			'z' => $packet->z,
		];
		$isNeedSendSound = $this->actionsNum['CRACK_BLOCK'] % 4 == 0;
		$this->actionsNum['CRACK_BLOCK']++;
		$pk = new LevelEventPacket();
		$pk->evid = LevelEventPacket::EVENT_PARTICLE_CRACK_BLOCK;
		$pk->x = $packet->x;
		$pk->y = $packet->y + 1;
		$pk->z = $packet->z;
		$pk->data = $blockId;
		foreach($recipients as $recipient){
			//$recipient->dataPacket($pk);
			if($isNeedSendSound){
				$recipient->sendSound(LevelSoundEventPacket::SOUND_HIT, $blockPos, 1, $blockId);
			}
		}
	}
	
	private function tryEnchant($transactionsData){
		foreach($transactionsData as $trData){
			if(!$trData->isUpdateEnchantSlotTransaction() || $trData->oldItem->getId() != Item::AIR){
				continue;
			}
			
			$transaction = $trData->convertToTransaction($this);
			$inventory = $transaction->getInventory();
			$inventory->setItem($transaction->getSlot(), $transaction->getTargetItem());
		}
	}
	
	public function sendSound($soundId, $position, $entityType = 1, $blockId = -1){
		$pk = new LevelSoundEventPacket();
		$pk->eventId = $soundId;
		$pk->x = $position['x'];
		$pk->y = $position['y'];
		$pk->z = $position['z'];
		$pk->blockId = $blockId;
		$pk->entityType = $entityType;
		$this->dataPacket($pk);
	}
	
	private function setMayMove($state){
		if($this->protocol >= ProtocolInfo::PROTOCOL_120){
			$this->setDataFlag(Player::DATA_FLAGS, 46, $state);
			$this->isMayMove = $state;
		}else{
			$this->isMayMove = true;
		}
	}

	public function customInteract($packet){
		
	}
	
	public function fall($fallDistance){
		if(!$this->allowFlight){
			parent::fall($fallDistance);
		}
	}
	
	protected function onJump(){
 		
 	}
	
	 protected function releaseUseItem(){
		if($this->startAction > -1 && $this->getDataFlag(Player::DATA_FLAGS, Player::DATA_FLAG_ACTION)){
			if($this->inventory->getItemInHand()->getId() === Item::BOW){
				$bow = $this->inventory->getItemInHand();
				if($this->isSurvival() || $this->isAdventure() && !$this->isCreative() && !$this->isSpectator() && !$this->inventory->contains(Item::get(Item::ARROW, 0, 1))){
					$this->inventory->sendContents($this);
					return;
				}

				$yawRad = $this->yaw / 180 * M_PI;
				$pitchRad = $this->pitch / 180 * M_PI;
				$nbt = new Compound("", [
					"Pos" => new Enum("Pos", [
						new DoubleTag("", $this->x),
						new DoubleTag("", $this->y + $this->getEyeHeight()),
						new DoubleTag("", $this->z)
							]),
					"Motion" => new Enum("Motion", [
						new DoubleTag("", -sin($yawRad) * cos($pitchRad)),
						new DoubleTag("", -sin($pitchRad)),
						new DoubleTag("", cos($yawRad) * cos($pitchRad))
							]),
					"Rotation" => new Enum("Rotation", [
						new FloatTag("", $this->yaw),
						new FloatTag("", $this->pitch)
							]),
					"Fire" => new ShortTag("Fire", $this->isOnFire() ? 45 * 60 : 0)
				]);

				$diff = ($this->server->getTick() - $this->startAction);
				$p = $diff / 20;
				$f = min((($p ** 2) + $p * 2) / 3, 1) * 2;
				$ev = new EntityShootBowEvent($this, $bow, Entity::createEntity("Arrow", $this->level, $nbt, $this, $f == 2 ? true : false), $f);

				if($f < 0.1 || $diff < 5){
					$ev->setCancelled();
				}

				$this->server->getPluginManager()->callEvent($ev);

				$projectile = $ev->getProjectile();
				if($ev->isCancelled()){
					$projectile->kill();
					$this->inventory->sendContents($this);
				}else{
					$projectile->setMotion($projectile->getMotion()->multiply($ev->getForce()));
					if($this->isSurvival() || $this->isAdventure()/* && !$this->isCreative() && !$this->isSpectator()*/){
						$this->inventory->removeItemWithCheckOffHand(Item::get(Item::ARROW, 0, 1));
						$bow->setDamage($bow->getDamage() + 1);
						if($bow->getDamage() >= 385){
							$this->inventory->setItemInHand(Item::get(Item::AIR, 0, 0));
						}else{
							$this->inventory->setItemInHand($bow);
						}
					}
					
					if($projectile instanceof Projectile){
						$this->server->getPluginManager()->callEvent($projectileEv = new ProjectileLaunchEvent($projectile));
						if($projectileEv->isCancelled()){
							$projectile->kill();
						}else{
							$projectile->spawnToAll();
							$recipients = $this->hasSpawned;
							$recipients[$this->id] = $this;
							$pk = new LevelSoundEventPacket();
							$pk->eventId = 20;
							$pk->x = $this->x;
							$pk->y = $this->y;
							$pk->z = $this->z;
							$pk->blockId = -1;
							$pk->entityType = 1;
							Server::broadcastPacket($recipients, $pk);
						}
					}else{
						$projectile->spawnToAll();
					}
				}
			}
		}elseif($this->inventory->getItemInHand()->getId() === Item::BUCKET && $this->inventory->getItemInHand()->getDamage() === 1){
			$this->server->getPluginManager()->callEvent($ev = new PlayerItemConsumeEvent($this, $this->inventory->getItemInHand()));
			if($ev->isCancelled()){
				$this->inventory->sendContents($this);
				return;
			}

			$pk = new EntityEventPacket();
			$pk->eid = $this->getId();
			$pk->event = EntityEventPacket::USE_ITEM;
			$this->dataPacket($pk);
			Server::broadcastPacket($this->getViewers(), $pk);

			if($this->isSurvival() || $this->isAdventure()/* && !$this->isCreative() && !$this->isSpectator()*/){
				$slot = $this->inventory->getItemInHand();
				--$slot->count;
				$this->inventory->setItemInHand($slot);
				$this->inventory->addItem(Item::get(Item::BUCKET, 0, 1));
			}

			$this->removeAllEffects();
		}else{
			$this->inventory->sendContents($this);
		}
	}
	
	public function getLoaderId(){
		return $this->loaderId;
	}
	
	public function getServerAddress(){
		return $this->serverAddress;
	}
	
	public function getClientlanguageCode(){
		return $this->languageCode;
	}
	
	public function getClientVersion(){
		return $this->clientVersion;
	}
	
	public function getOriginalProtocol(){
		return $this->originalProtocol;
	}
	
	public function showModal($modalWindow){
		if($this->protocol >= Info::PROTOCOL_120){
			$pk = new ShowModalFormPacket();
			$pk->formId = $this->lastModalId++;
			$pk->data = $modalWindow->toJSON();
			$this->dataPacket($pk);
			$this->activeModalWindows[$pk->formId] = $modalWindow; 
			return true;
		}
		
		return false;
	}

	public function checkModal($formId, $data){
		if(isset($this->activeModalWindows[$formId])){
			$this->activeModalWindows[$formId]->handle($data, $this);
			unset($this->activeModalWindows[$formId]);
		}
	}
	
	protected function sendServerSettingsModal($modalWindow){
		if($this->protocol >= Info::PROTOCOL_120){
			$pk = new ServerSettingsResponsetPacket();
			$pk->formId = $this->lastModalId++;
			$pk->data = $modalWindow->toJSON();
			$this->dataPacket($pk);
			$this->activeModalWindows[$pk->formId] = $modalWindow;
		}
	}

	protected function sendServerSettings(){
		
	}
	
	public function needEncrypt(){
		return $this->protocol >= Info::PROTOCOL_120;
	}
	
	public function updatePlayerSkin($oldSkinName, $newSkinName){
		$pk = new RemoveEntityPacket();
		$pk->eid = $this->getId();

		$pk2 = new PlayerListPacket();
		$pk2->type = PlayerListPacket::TYPE_REMOVE;
		$pk2->entries[] = [$this->getUniqueId()];

		$pk3 = new PlayerListPacket();
		$pk3->type = PlayerListPacket::TYPE_ADD;
		$pk3->entries[] = [$this->getUniqueId(), $this->getId(), $this->getName(), $this->skinName, /*$this->skinId, */$this->skin, $this->capeData, $this->skinGeometryName, /*$this->skinGeometryId, */$this->skinGeometryData, $this->getXUID()];

		$pk4 = new AddPlayerPacket();
		$pk4->uuid = $this->getUniqueId();
		$pk4->username = $this->getName();
		$pk4->eid = $this->getId();
		$pk4->x = $this->x;
		$pk4->y = $this->y;
		$pk4->z = $this->z;
		$pk4->speedX = $this->motionX;
		$pk4->speedY = $this->motionY;
		$pk4->speedZ = $this->motionZ;
		$pk4->yaw = $this->yaw;
		$pk4->pitch = $this->pitch;
		$pk4->metadata = $this->dataProperties;
		
		$pk120 = new PlayerSkinPacket();
		$pk120->uuid = $this->getUniqueId();
		$pk120->newSkinId = $this->skinName;
		$pk120->newSkinName = $newSkinName;
		$pk120->oldSkinName = $oldSkinName;
		//$pk120->newSkinId = $newSkinId;
		//$pk120->oldSkinName = $oldSkinId;
		$pk120->newSkinByteData = $this->skin;
		$pk120->newCapeByteData = $this->capeData;
		$pk120->newSkinGeometryName = $this->skinGeometryName;
		//$pk120->newSkinGeometryId = $this->skinGeometryId;
		$pk120->newSkinGeometryData = $this->skinGeometryData;
		
		$viewers120 = [];
		$oldViewers = [];
		$recipients = $this->getViewers();
		$recipients[] = $this;
		foreach($recipients as $viewer){
			if($viewer->getPlayerProtocol() >= ProtocolInfo::PROTOCOL_120){
				$viewers120[] = $viewer;
			}else{
				$oldViewers[] = $viewer;
			}
		}
		
		if(!empty($viewers120)){
			$this->server->batchPackets($viewers120, [$pk120]);
		}
		
		if(!empty($oldViewers)){
			$this->server->batchPackets($oldViewers, [$pk, $pk2, $pk3, $pk4]);
		}
	}
}
