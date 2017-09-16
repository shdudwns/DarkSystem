<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\tile;

use pocketmine\event\Timings;
use pocketmine\level\format\Chunk;
use pocketmine\level\format\FullChunk;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\utils\ChunkException;

abstract class Tile extends Position{
	
	const BED = "Bed";
	const SIGN = "Sign";
	const SKULL = "Skull";
	const CHEST = "Chest";
	const FURNACE = "Furnace";
	const FLOWER_POT = "FlowerPot";
	const MOB_SPAWNER = "MobSpawner";
	const ITEM_FRAME = "ItemFrame";
	const DISPENSER = "Dispenser";
	const DROPPER = "Dropper";
	const CAULDRON = "Cauldron";
	const HOPPER = "Hopper";
	const BEACON = "Beacon";
	const BREWING_STAND = "BrewingStand";
	const ENCHANT_TABLE = "EnchantTable";
	const DL_DETECTOR = "DayLightDetector";
	const ENDER_CHEST = "EnderChest";

	public static $tileCount = 1;

	private static $knownTiles = [];
	private static $shortNames = [];
	
	public $chunk;
	public $name;
	public $id;
	public $x;
	public $y;
	public $z;
	public $attach;
	public $metadata;
	public $closed = false;
	public $namedtag;
	
	protected $lastUpdate;
	protected $server;
	protected $timings;
	
	public $tickTimer;
	
	public static function init(){
		Tile::registerTile(Beacon::class);
		Tile::registerTile(Bed::class);
		Tile::registerTile(BrewingStand::class);
		Tile::registerTile(Cauldron::class);
		Tile::registerTile(Chest::class);
		//Tile::registerTile(CommandBlock::class);
		Tile::registerTile(Dispenser::class);
		Tile::registerTile(DLDetector::class);
		Tile::registerTile(Dropper::class);
		Tile::registerTile(EnchantTable::class);
		Tile::registerTile(EnderChest::class);
		Tile::registerTile(FlowerPot::class);
		Tile::registerTile(Furnace::class);
		Tile::registerTile(Hopper::class);
		Tile::registerTile(ItemFrame::class);
		//Tile::registerTile(MobSpawner::class);
		Tile::registerTile(Sign::class);
		Tile::registerTile(Skull::class);
	}
	
	public static function createTile($type, Level $level, Compound $nbt, ...$args){
		if(isset(Tile::$knownTiles[$type])){
			$class = Tile::$knownTiles[$type];
			return new $class($level, $nbt, ...$args);
		}

		return null;
	}
	
	public static function registerTile($className){
		$class = new \ReflectionClass($className);
		if(is_a($className, Tile::class, true) and !$class->isAbstract()){
			Tile::$knownTiles[$class->getShortName()] = $className;
			Tile::$shortNames[$className] = $class->getShortName();
			return true;
		}

		return false;
	}
	
	public function getSaveId(){
		return Tile::$shortNames[static::class];
	}

	public function __construct(Level $level, Compound $nbt){
		$this->timings = Timings::getTileEntityTimings($this);

		$this->namedtag = $nbt;
        $this->server = $level->getServer();
        $this->setLevel($level);
        $this->chunk = $level->getChunk($this->namedtag["x"] >> 4, $this->namedtag["z"] >> 4, false);
        assert($this->chunk !== null);

		$this->name = "";
		$this->lastUpdate = microtime(true);
		$this->id = Tile::$tileCount++;
		$this->x = (int) $this->namedtag["x"];
		$this->y = (int) $this->namedtag["y"];
		$this->z = (int) $this->namedtag["z"];

		$this->chunk->addTile($this);
		$level->addTile($this);
		$this->tickTimer = Timings::getTileEntityTimings($this);
	}

	public function getId(){
		return $this->id;
	}

	public function saveNBT(){
		$this->namedtag->id = new StringTag("id", $this->getSaveId());
		$this->namedtag->x = new IntTag("x", $this->x);
		$this->namedtag->y = new IntTag("y", $this->y);
		$this->namedtag->z = new IntTag("z", $this->z);
	}
	
	public function getBlock(){
		return $this->level->getBlock($this);
	}

	public function onUpdate(){
		return false;
	}

	public final function scheduleUpdate(){
		$this->level->updateTiles[$this->id] = $this;
	}

	public function __destruct(){
		$this->close();
	}

	public function close(){
		if(!$this->closed){
			$this->closed = true;
			unset($this->level->updateTiles[$this->id]);
			if($this->chunk instanceof FullChunk){
				$this->chunk->removeTile($this);
			}
			
			if(($level = $this->getLevel()) instanceof Level){
				$level->removeTile($this);
			}
			
			$this->level = null;
		}
	}

	public function getName(){
		return $this->name;
	}

}
