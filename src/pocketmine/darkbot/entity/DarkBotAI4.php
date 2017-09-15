<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\darkbot\entity;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\PlayerInventory;
use pocketmine\item\Item as ItemItem;
use pocketmine\network\protocol\PlayerListPacket;
use pocketmine\utils\UUID;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\Network;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\RemoveEntityPacket;
use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\entity\Attribute;
use pocketmine\entity\Creature;
use pocketmine\entity\ProjectileSource;
use pocketmine\network\multiversion\Multiversion;

class DarkBotAI4 extends Creature implements ProjectileSource, InventoryHolder{

	protected $nameTag = "TESTIFICATE";
	/** @var PlayerInventory */
	protected $inventory;
	
	/** @var UUID */
	protected $uuid;
	protected $rawUUID;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 1.8;
	public $eyeHeight = 1.62;
	
	protected $skinName = 'Standard_Custom';
	//protected $skinId;
	protected $skin;
	protected $skinGeometryName = "geometry.humanoid.custom";
	//protected $skinGeometryId = "";
	protected $skinGeometryData = "";
	protected $capeData = "";
	
	protected $totalXp = 0;
	protected $xpSeed;
	protected $xpCooldown = 0;
	
	public function getSkinName(){
		return $this->skinName;
	}
	
	/*public function getSkinId(){
		return $this->skinId;
	}*/
	
	public function getSkinData(){
		return $this->skin;
	}
	
	public function getSkinGeometryName(){
		return $this->skinGeometryName;
	}
	
	/*public function getSkinGeometryId(){
		return $this->skinGeometryId;
	}*/
	
	public function getSkinGeometryData(){
		return $this->skinGeometryData;
	}
	
	public function getCapeData(){
		return $this->capeData;
	}
	
	/**
	 * @return UUID|null
	 */
	public function getUniqueId(){
		return $this->uuid;
	}

	/**
	 * @return string
	 */
	public function getRawUniqueId(){
		return $this->rawUUID;
	}

	/**
	 * @param string $str
	// * @param string $skinId
	 * @param bool   $skinName
	 */
	public function setSkin($str, /*$skinId, */$skinName, $skinGeometryName = "", /*$skinGeometryId = "", */$skinGeometryData = "", $capeData = ""){
		$this->skin = $str;
		//$this->skinId = $skinId;
		if(is_string($skinName)){
			$this->skinName = $skinName;
		}
		
		if(!empty($skinGeometryName)){
			$this->skinGeometryName = $skinGeometryName;
		}
		
		/*if(!empty($skinGeometryId)){
			$this->skinGeometryId = $skinGeometryId;
		}*/
		
		if(!empty($skinGeometryData)){
			$this->skinGeometryData = $skinGeometryData;
		}
		
		if(!empty($capeData)){
			$this->capeData = $capeData;
		}
	}
	
	/*public function getSaturation(){
		return $this->attributeMap->getAttribute(Attribute::SATURATION)->getValue();
	}
	
	public function setSaturation($saturation){
		$this->attributeMap->getAttribute(Attribute::SATURATION)->setValue($saturation);
	}
	
	public function addSaturation($amount){
		$attr = $this->attributeMap->getAttribute(Attribute::SATURATION);
		$attr->setValue($attr->getValue() + $amount, true);
	}
	
	public function getExhaustion(){
		return $this->attributeMap->getAttribute(Attribute::EXHAUSTION)->getValue();
	}
	
	public function setExhaustion($exhaustion){
		$this->attributeMap->getAttribute(Attribute::EXHAUSTION)->setValue($exhaustion);
	}
	
	public function exhaust($amount, $cause = PlayerExhaustEvent::CAUSE_CUSTOM){
		$this->server->getPluginManager()->callEvent($ev = new PlayerExhaustEvent($this, $amount, $cause));
		if($ev->isCancelled()){
			return 0.0;
		}

		$exhaustion = $this->getExhaustion();
		$exhaustion += $ev->getAmount();
		
		while($exhaustion >= 4.0){
			$exhaustion -= 4.0;
			$saturation = $this->getSaturation();
			if($saturation > 0){
				$saturation = max(0, $saturation - 1.0);
				$this->setSaturation($saturation);
			}else{
				$food = $this->getFood();
				if($food > 0){
					$food--;
					$this->setFood($food);
				}
			}
		}
		
		$this->setExhaustion($exhaustion);
		return $ev->getAmount();
	}
	
	public function getXpLevel(){
		return (int) $this->attributeMap->getAttribute(Attribute::EXPERIENCE_LEVEL)->getValue();
	}
	
	public function setXpLevel($level){
		$this->server->getPluginManager()->callEvent($ev = new PlayerExperienceChangeEvent($this, $level, $this->getXpProgress()));
		if(!$ev->isCancelled()){
			$this->attributeMap->getAttribute(Attribute::EXPERIENCE_LEVEL)->setValue($ev->getExpLevel());
			return true;
		}
		
		return false;
	}
	
	public function addXpLevel($level){
		return $this->setXpLevel($this->getXpLevel() + $level);
	}
	
	public function takeXpLevel($level){
		return $this->setXpLevel($this->getXpLevel() - $level);
	}
	
	public function getXpProgress(){
		return $this->attributeMap->getAttribute(Attribute::EXPERIENCE)->getValue();
	}
	
	public function setXpProgress($progress){
		$this->attributeMap->getAttribute(Attribute::EXPERIENCE)->setValue($progress);
		return true;
	}
	
	public function getTotalXp(){
		return $this->totalXp;
	}
	
	public function setTotalXp($xp, $syncLevel = false){
		$xp &= 0x7fffffff;
		if($xp === $this->totalXp){
			return false;
		}
		
		if(!$syncLevel){
			$level = $this->getXpLevel();
			$diff = $xp - $this->totalXp + $this->getFilledXp();
			if($diff > 0){
				while($diff > ($v = self::getLevelXpRequirement($level))){
					$diff -= $v;
					if(++$level >= 21863){
						$diff = $v;
						break;
					}
				}
			}else{
				while($diff < ($v = self::getLevelXpRequirement($level - 1))){
					$diff += $v;
					if(--$level <= 0){
						$diff = 0;
						break;
					}
				}
			}
			
			$progress = ($diff / $v);
		}else{
			$values = self::getLevelFromXp($xp);
			$level = $values[0];
			$progress = $values[1];
		}

		$this->server->getPluginManager()->callEvent($ev = new PlayerExperienceChangeEvent($this, $level, $progress));
		if(!$ev->isCancelled()){
			$this->totalXp = $xp;
			$this->setXpLevel($ev->getExpLevel());
			$this->setXpProgress($ev->getProgress());
			return true;
		}
		
		return false;
	}
	
	public function addXp($xp, $syncLevel = false){
		return $this->setTotalXp($this->totalXp + $xp, $syncLevel);
	}
	
	public function takeXp($xp, $syncLevel = false){
		return $this->setTotalXp($this->totalXp - $xp, $syncLevel);
	}
	
	public function getRemainderXp(){
		return self::getLevelXpRequirement($this->getXpLevel()) - $this->getFilledXp();
	}
	
	public function getFilledXp(){
		return self::getLevelXpRequirement($this->getXpLevel()) * $this->getXpProgress();
	}
	
	public function recalculateXpProgress(){
		$this->setXpProgress($progress = $this->getRemainderXp() / self::getLevelXpRequirement($this->getXpLevel()));
		return $progress;
	}
	
	public function getXpSeed(){
		return $this->xpSeed;
	}

	public function resetXpCooldown(){
		$this->xpCooldown = microtime(true);
	}
	
	public function canPickupXp(){
		return microtime(true) - $this->xpCooldown > 0.5;
	}
	
	public static function getTotalXpRequirement($level){
		if($level <= 16){
			return ($level ** 2) + (6 * $level);
		}elseif($level <= 31){
			return (2.5 * ($level ** 2)) - (40.5 * $level) + 360;
		}elseif($level <= 21863){
			return (4.5 * ($level ** 2)) - (162.5 * $level) + 2220;
		}
		
		return PHP_INT_MAX;
	}
	
	public static function getLevelXpRequirement($level){
		if($level <= 16){
			return (2 * $level) + 7;
		}elseif($level <= 31){
			return (5 * $level) - 38;
		}elseif($level <= 21863){
			return (9 * $level) - 158;
		}
		
		return PHP_INT_MAX;
	}
	
	public static function getLevelFromXp($xp){
		$xp &= 0x7fffffff;
		$a = 1;
		$b = 6;
		$c = -$xp;
		if($xp > self::getTotalXpRequirement(16)){
			if($xp <= self::getTotalXpRequirement(31)){
				$a = 2.5;
				$b = -40.5;
				$c += 360;
			}else{
				$a = 4.5;
				$b = -162.5;
				$c += 2220;
			}
		}

		$answer = max(Math::solveQuadratic($a, $b, $c));
		$level = floor($answer);
		$progress = $answer - $level;
		return [$level, $progress];
	}*/
	
	public function getInventory(){
		return $this->inventory;
	}

	protected function initEntity(){
		$this->setDataFlag(self::DATA_PLAYER_FLAGS, self::DATA_PLAYER_FLAG_SLEEP, false);
		$this->setDataProperty(self::DATA_PLAYER_BED_POSITION, self::DATA_TYPE_POS, [0, 0, 0]);
		if ($this instanceof Player){
			$this->inventory = Multiversion::getPlayerInventory($this);
			$this->addWindow($this->inventory, 0);
		} else {
			$this->inventory = new PlayerInventory($this);
		}
		if(!($this instanceof Player)){
			if(isset($this->namedtag->NameTag)){
				$this->setNameTag($this->namedtag["NameTag"]);
			}
			if(isset($this->namedtag->Skin) and $this->namedtag->Skin instanceof Compound){
				$this->setSkin($this->namedtag->Skin["Data"], /*$this->namedtag->Skin["Name"], */$this->namedtag->Skin["Slim"] > 0);
			}
			$this->uuid = UUID::fromData($this->getId(), $this->getSkinData(), $this->getNameTag());
		}
		if(isset($this->namedtag->Inventory) and $this->namedtag->Inventory instanceof Enum){
			foreach($this->namedtag->Inventory as $item){
				if($item["Slot"] >= 0 and $item["Slot"] < 9){ //Hotbar
					$this->inventory->setHotbarSlotIndex($item["Slot"], isset($item["TrueSlot"]) ? $item["TrueSlot"] : -1);
				}elseif($item["Slot"] >= 100 and $item["Slot"] < 104){ //Armor
					$this->inventory->setItem($this->inventory->getSize() + $item["Slot"] - 100, NBT::getItemHelper($item));
				}else{
					$this->inventory->setItem($item["Slot"] - 9, NBT::getItemHelper($item));
				}
			}
		}
		parent::initEntity();
		/*if(!isset($this->namedtag->XpLevel) or !($this->namedtag->XpLevel instanceof IntTag)){
			$this->namedtag->XpLevel = new IntTag("XpLevel", 0);
		}
		$this->setXpLevel($this->namedtag["XpLevel"]);
		
		if(!isset($this->namedtag->XpP) or !($this->namedtag->XpP instanceof FloatTag)){
			$this->namedtag->XpP = new FloatTag("XpP", 0);
		}
		$this->setXpProgress($this->namedtag["XpP"]);
		
		if(!isset($this->namedtag->XpTotal) or !($this->namedtag->XpTotal instanceof IntTag)){
			$this->namedtag->XpTotal = new IntTag("XpTotal", 0);
		}
		$this->totalXp = $this->namedtag["XpTotal"];
		
		if(!isset($this->namedtag->XpSeed) or !($this->namedtag->XpSeed instanceof IntTag)){
			$this->namedtag->XpSeed = new IntTag("XpSeed", mt_rand(PHP_INT_MIN, PHP_INT_MAX));
		}
		$this->xpSeed = $this->namedtag["XpSeed"];*/
	}

	public function getName(){
		return $this->getNameTag();
	}

	public function getDrops(){
		$drops = [];
		if($this->inventory !== null){
			foreach($this->inventory->getContents() as $item){
				$drops[] = $item;
			}
		}

		return $drops;
	}

	public function saveNBT(){
		parent::saveNBT();
		$this->namedtag->Inventory = new Enum("Inventory", []);
		$this->namedtag->Inventory->setTagType(NBT::TAG_Compound);
		if($this->inventory !== null){
			for($slot = 0; $slot < 9; ++$slot){
				$hotbarSlot = $this->inventory->getHotbarSlotIndex($slot);
				if($hotbarSlot !== -1){
					$item = $this->inventory->getItem($hotbarSlot);
					if($item->getId() !== 0 and $item->getCount() > 0){
						$this->namedtag->Inventory[$slot] = new Compound(false, [
							new ByteTag("Count", $item->getCount()),
							new ShortTag("Damage", $item->getDamage()),
							new ByteTag("Slot", $slot),
							new ByteTag("TrueSlot", $hotbarSlot),
							new ShortTag("id", $item->getId()),
						]);
						continue;
					}
				}
				
				$this->namedtag->Inventory[$slot] = new Compound(false, [
					new ByteTag("Count", 0),
					new ShortTag("Damage", 0),
					new ByteTag("Slot", $slot),
					new ByteTag("TrueSlot", -1),
					new ShortTag("id", 0),
				]);
			}
			
			$slotCount = Player::SURVIVAL_SLOTS + 9;
			for($slot = 9; $slot < $slotCount; ++$slot){
				$item = $this->inventory->getItem($slot - 9);
				$this->namedtag->Inventory[$slot] = new Compound(false, [
					new ByteTag("Count", $item->getCount()),
					new ShortTag("Damage", $item->getDamage()),
					new ByteTag("Slot", $slot),
					new ShortTag("id", $item->getId()),
				]);
			}
			
			for($slot = 100; $slot < 104; ++$slot){
				$item = $this->inventory->getItem($this->inventory->getSize() + $slot - 100);
				if($item instanceof ItemItem and $item->getId() !== ItemItem::AIR){
					$this->namedtag->Inventory[$slot] = new Compound(false, [
						new ByteTag("Count", $item->getCount()),
						new ShortTag("Damage", $item->getDamage()),
						new ByteTag("Slot", $slot),
						new ShortTag("id", $item->getId()),
					]);
				}
			}
		}
	}

	public function spawnTo(Player $player){
		if($player !== $this and !isset($this->hasSpawned[$player->getId()]) and isset($player->usedChunks[Level::chunkHash($this->chunk->getX(), $this->chunk->getZ())])){
			$pk1 = new PlayerListPacket();
			$pk1->type = PlayerListPacket::TYPE_ADD;
			$pk1->entries[] = [$this->getUniqueId(), $this->getId(), $this->getName(), $this->skinName, $this->skin, $this->skinGeometryName, $this->skinGeometryData, $this->capeData];
			$this->hasSpawned[$player->getId()] = $player;
			
			$xuid = ($this instanceof Player) ? $this->getXUID() : "";
			$this->server->updatePlayerListData($this->getUniqueId(), $this->getId(), $this->getName(), $this->skinName, /*$this->skinId, */$this->skin, $this->skinGeometryName, /*$this->skinGeometryId, */$this->skinGeometryData, $this->capeData, $xuid, [$player]);
			
			$pk2 = new AddPlayerPacket();
			$pk2->uuid = $this->getUniqueId();
			$pk2->username = $this->getName();
			$pk2->eid = $this->getId();
			$pk2->x = $this->server->getDefaultLevel()->getSafeSpawn()->x;
			$pk2->y = $this->server->getDefaultLevel()->getSafeSpawn()->y;
			$pk2->z = $this->server->getDefaultLevel()->getSafeSpawn()->z;
			$pk2->speedX = $this->motionX;
			$pk2->speedY = $this->motionY;
			$pk2->speedZ = $this->motionZ;
			$pk2->yaw = $this->yaw;
			$pk2->pitch = $this->pitch;
			$pk2->item = $this->inventory->getItemInHand();
			$pk2->metadata = $this->dataProperties;
			$player->dataPacket($pk1);
			$player->dataPacket($pk2);

			$this->inventory->sendArmorContents($player);
			$this->level->addPlayerHandItem($this, $player);

			if(!($this instanceof Player)){
				$this->server->removePlayerListData($this->getUniqueId(), [$player]);
			}
		}
	}

	public function despawnFrom(Player $player){
		if(isset($this->hasSpawned[$player->getId()])){
			$pk = new RemoveEntityPacket();
			$pk->eid = $this->getId();
			$player->dataPacket($pk);
			unset($this->hasSpawned[$player->getId()]);
		}
	}

	public function close(){
		if(!$this->closed){
			if(!($this instanceof Player) or $this->loggedIn){
				foreach($this->inventory->getViewers() as $viewer){
					$viewer->removeWindow($this->inventory);
				}
			}
			
			parent::close();
		}
	}
	
	public function isNeedSaveOnChunkUnload(){
		return true;
	}
	
}
