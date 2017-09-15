<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\block;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\level\Level;
use pocketmine\level\MovingObjectPosition;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\metadata\Metadatable;
use pocketmine\metadata\MetadataValue;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class Block extends Position implements Metadatable{
	
	const AIR = 0;
	const STONE = 1;
	const GRASS = 2;
	const DIRT = 3;
	const COBBLESTONE = 4;
	const COBBLE = 4;
	const PLANK = 5;
	const PLANKS = 5;
	const WOODEN_PLANK = 5;
	const WOODEN_PLANKS = 5;
	const SAPLING = 6;
	const SAPLINGS = 6;
	const BEDROCK = 7;
	const WATER = 8;
	const STILL_WATER = 9;
	const LAVA = 10;
	const STILL_LAVA = 11;
	const SAND = 12;
	const GRAVEL = 13;
	const GOLD_ORE = 14;
	const IRON_ORE = 15;
	const COAL_ORE = 16;
	const WOOD = 17;
	const TRUNK = 17;
	const LOG = 17;
	const LEAVES = 18;
	const LEAVE = 18;
	const SPONGE = 19;
	const GLASS = 20;
	const LAPIS_ORE = 21;
	const LAPIS_BLOCK = 22;
	const DISPENSER = 23;
	const SANDSTONE = 24;
	const NOTE_BLOCK = 25;
	const BED_BLOCK = 26;
	const POWERED_RAIL = 27;
	const DETECTOR_RAIL = 28;
	const STICKY_PISTON = 29;
	const COBWEB = 30;
	const TALL_GRASS = 31;
	const BUSH = 32;
	const DEAD_BUSH = 32;
	const PISTON = 33;
	const PISTON_HEAD = 34;
	const WOOL = 35;
	const DANDELION = 37;
	const ROSE = 38;
	const POPPY = 38;
	const RED_FLOWER = 38;
	const BROWN_MUSHROOM = 39;
	const RED_MUSHROOM = 40;
	const GOLD_BLOCK = 41;
	const IRON_BLOCK = 42;
	const DOUBLE_SLAB = 43;
	const DOUBLE_SLABS = 43;
	const SLAB = 44;
	const SLABS = 44;
	const STONE_SLAB = 44;
	const BRICKS = 45;
	const BRICKS_BLOCK = 45;
	const TNT = 46;
	const BOOKSHELF = 47;
	const MOSS_STONE = 48;
	const MOSSY_STONE = 48;
	const OBSIDIAN = 49;
	const TORCH = 50;
	const FIRE = 51;
	const MONSTER_SPAWNER = 52;
	const WOOD_STAIRS = 53;
	const WOODEN_STAIRS = 53;
	const OAK_WOOD_STAIRS = 53;
	const OAK_WOODEN_STAIRS = 53;
	const CHEST = 54;
	const REDSTONE_WIRE = 55;
	const DIAMOND_ORE = 56;
	const DIAMOND_BLOCK = 57;
	const CRAFTING_TABLE = 58;
	const WORKBENCH = 58;
	const WHEAT_BLOCK = 59;
	const FARMLAND = 60;
	const FURNACE = 61;
	const BURNING_FURNACE = 62;
	const LIT_FURNACE = 62;
	const SIGN_POST = 63;
	const DOOR_BLOCK = 64;
	const WOODEN_DOOR_BLOCK = 64;
	const WOOD_DOOR_BLOCK = 64;
	const LADDER = 65;
	const RAIL = 66;
	const COBBLE_STAIRS = 67;
	const COBBLESTONE_STAIRS = 67;
	const WALL_SIGN = 68;
	const LEVER = 69;
	const STONE_PRESSURE_PLATE = 70;
	const IRON_DOOR_BLOCK = 71;
	const WOODEN_PRESSURE_PLATE = 72;
	const REDSTONE_ORE = 73;
	const GLOWING_REDSTONE_ORE = 74;
	const LIT_REDSTONE_ORE = 74;
	const REDSTONE_TORCH = 75;
	const REDSTONE_TORCH_ACTIVE = 76;
	const STONE_BUTTON = 77;
	const SNOW = 78;
	const SNOW_LAYER = 78;
	const ICE = 79;
	const SNOW_BLOCK = 80;
	const CACTUS = 81;
	const CLAY_BLOCK = 82;
	const REEDS = 83;
	const SUGARCANE_BLOCK = 83;
	const FENCE = 85;
	const PUMPKIN = 86;
	const NETHERRACK = 87;
	const SOUL_SAND = 88;
	const GLOWSTONE = 89;
	const GLOWSTONE_BLOCK = 89;
	const PORTAL = 90;
	const LIT_PUMPKIN = 91;
	const JACK_O_LANTERN = 91;
	const CAKE_BLOCK = 92;
	const REDSTONE_REPEATER_BLOCK = 93;
	const REDSTONE_REPEATER_BLOCK_ACTIVE = 94;
	const INVISIBLE_BEDROCK = 95;
	const TRAPDOOR = 96;
	const WOODEN_TRAPDOOR = 96;
	const WOOD_TRAPDOOR = 96;
	const MONSTER_EGG = 97;
	const STONE_BRICKS = 98;
	const STONE_BRICK = 98;
	const STONEBRICK = 98;
	const BROWN_MUSHROOM_BLOCK = 99;
	const RED_MUSHROOM_BLOCK = 100;
	const IRON_BAR = 101;
	const IRON_BARS = 101;
	const GLASS_PANE = 102;
	const GLASS_PANEL = 102;
	const MELON_BLOCK = 103;
	const PUMPKIN_STEM = 104;
	const MELON_STEM = 105;
	const VINE = 106;
	const VINES = 106;
	const FENCE_GATE = 107;
	const BRICK_STAIRS = 108;
	const STONE_BRICK_STAIRS = 109;
	const MYCELIUM = 110;
	const WATER_LILY = 111;
	const LILY_PAD = 111;
	const NETHER_BRICKS = 112;
	const NETHER_BRICK_BLOCK = 112;
	const NETHER_BRICK_FENCE = 113;
	const NETHER_BRICKS_STAIRS = 114;
	const NETHER_WART_BLOCK = 115;
	const ENCHANTING_TABLE = 116;
	const ENCHANT_TABLE = 116;
	const ENCHANTMENT_TABLE = 116;
	const BREWING_STAND_BLOCK = 117;
	const CAULDRON_BLOCK = 118;
	const END_PORTAL = 119;
	const END_PORTAL_FRAME = 120;
	const END_STONE = 121;
	const DRAGON_EGG = 122;
	const REDSTONE_LAMP = 123;
	const REDSTONE_LAMP_ACTIVE = 124;
	const DROPPER = 125;
	const ACTIVATOR_RAIL = 126;
	const COCOA = 127;
	const SANDSTONE_STAIRS = 128;
	const EMERALD_ORE = 129;
    const ENDER_CHEST = 130;
	const TRIPWIRE_HOOK = 131;
	const TRIPWIRE = 132;
	const EMERALD_BLOCK = 133;
	const SPRUCE_WOOD_STAIRS = 134;
	const SPRUCE_WOODEN_STAIRS = 134;
	const BIRCH_WOOD_STAIRS = 135;
	const BIRCH_WOODEN_STAIRS = 135;
	const JUNGLE_WOOD_STAIRS = 136;
	const JUNGLE_WOODEN_STAIRS = 136;
	const COBBLE_WALL = 139;
	const STONE_WALL = 139;
	const COBBLESTONE_WALL = 139;
	const FLOWER_POT_BLOCK = 140;
	const CARROT_BLOCK = 141;
	const POTATO_BLOCK = 142;
	const WOODEN_BUTTON = 143;
	const MOB_HEAD_BLOCK = 144;
	const ANVIL = 145;
	const TRAPPED_CHEST = 146;
	const WEIGHTED_PRESSURE_PLATE_LIGHT = 147;
	const WEIGHTED_PRESSURE_PLATE_HEAVY = 148;
	const REDSTONE_COMPARATOR_BLOCK = 149;
	const REDSTONE_COMPARATOR_BLOCK_POWERED = 150;
	const DAYLIGHT_SENSOR = 151;
	const REDSTONE_BLOCK = 152;
	const NETHER_QUARTZ_ORE = 153;
	const HOPPER_BLOCK = 154;
	const QUARTZ_BLOCK = 155;
	const QUARTZ_STAIRS = 156;
	const DOUBLE_WOOD_SLAB = 157;
	const DOUBLE_WOODEN_SLAB = 157;
	const DOUBLE_WOOD_SLABS = 157;
	const DOUBLE_WOODEN_SLABS = 157;
	const WOOD_SLAB = 158;
	const WOODEN_SLAB = 158;
	const WOOD_SLABS = 158;
	const WOODEN_SLABS = 158;
	const STAINED_CLAY = 159;
	const STAINED_HARDENED_CLAY = 159;
	const STAINED_GLASS_PANE = 160;
	const LEAVES2 = 161;
	const LEAVE2 = 161;
	const WOOD2 = 162;
	const TRUNK2 = 162;
	const LOG2 = 162;
	const ACACIA_WOOD_STAIRS = 163;
	const ACACIA_WOODEN_STAIRS = 163;
	const DARK_OAK_WOOD_STAIRS = 164;
	const DARK_OAK_WOODEN_STAIRS = 164;
	const SLIME_BLOCK = 165;
	const IRON_TRAPDOOR = 167;
	const HAY_BALE = 170;
	const CARPET = 171;
	const HARDENED_CLAY = 172;
	const COAL_BLOCK = 173;
	const PACKED_ICE = 174;
	const DOUBLE_PLANT = 175;
	const INVERTED_DAYLIGHT_SENSOR = 178;
	const RED_SANDSTONE = 179;
	const RED_SANDSTONE_STAIRS = 180;
	const DOUBLE_RED_SANDSTONE_SLAB = 181;
	const RED_SANDSTONE_SLAB = 182;
	const FENCE_GATE_SPRUCE = 183;
	const FENCE_GATE_BIRCH = 184;
	const FENCE_GATE_JUNGLE = 185;
	const FENCE_GATE_DARK_OAK = 186;
	const FENCE_GATE_ACACIA = 187;
	const SPRUCE_DOOR_BLOCK = 193;
	const BIRCH_DOOR_BLOCK = 194;
	const JUNGLE_DOOR_BLOCK = 195;
	const ACACIA_DOOR_BLOCK = 196;
	const DARK_OAK_DOOR_BLOCK = 197;
	const GRASS_PATH = 198;
	const ITEM_FRAME_BLOCK = 199;
    const CHORUS_FLOWER = 200;
    const PURPUR_BLOCK = 201;
    const END_BRICKS = 206;
    const END_ROD = 208;
    const END_GATEWAY = 209;
    const CHORUS_PLANT = 240;
    const STAINED_GLASS = 241;
	const PODZOL = 243;
	const BEETROOT_BLOCK = 244;
	const STONECUTTER = 245;
	const GLOWING_OBSIDIAN = 246;
	const NETHER_REACTOR = 247;

	/** @var \SplFixedArray */
	public static $list = null;
	/** @var \SplFixedArray */
	public static $fullList = null;

	/** @var \SplFixedArray */
	public static $light = null;
	/** @var \SplFixedArray */
	public static $lightFilter = null;
	/** @var \SplFixedArray */
	public static $solid = null;
	/** @var \SplFixedArray */
	public static $hardness = null;
	/** @var \SplFixedArray */
	public static $transparent = null;

	protected $id;
	protected $meta = 0;

	/** @var AxisAlignedBB */
	public $boundingBox = null;

	/**
	 * @deprecated
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get($key){
		static $map = [
			"hardness" => "getHardness",
			"lightLevel" => "getLightLevel",
			"frictionFactor" => "getFrictionFactor",
			"name" => "getName",
			"isPlaceable" => "canBePlaced",
			"isReplaceable" => "canBeReplaced",
			"isTransparent" => "isTransparent",
			"isSolid" => "isSolid",
			"isFlowable" => "canBeFlowedInto",
			"isActivable" => "canBeActivated",
			"hasEntityCollision" => "hasEntityCollision"
		];
		return isset($map[$key]) ? $this->{$map[$key]}() : null;
	}

	public static function init(){
		if(Block::$list === null){
			Block::$list = new \SplFixedArray(256);
			Block::$fullList = new \SplFixedArray(4096);
			Block::$light = new \SplFixedArray(256);
			Block::$lightFilter = new \SplFixedArray(256);
			Block::$solid = new \SplFixedArray(256);
			Block::$hardness = new \SplFixedArray(256);
			Block::$transparent = new \SplFixedArray(256);
			Block::$list[Block::AIR] = Air::class;
			Block::$list[Block::STONE] = Stone::class;
			Block::$list[Block::GRASS] = Grass::class;
			Block::$list[Block::DIRT] = Dirt::class;
			Block::$list[Block::COBBLESTONE] = Cobblestone::class;
			Block::$list[Block::PLANKS] = Planks::class;
			Block::$list[Block::SAPLING] = Sapling::class;
			Block::$list[Block::BEDROCK] = Bedrock::class;
			Block::$list[Block::WATER] = Water::class;
			Block::$list[Block::STILL_WATER] = StillWater::class;
			Block::$list[Block::LAVA] = Lava::class;
			Block::$list[Block::STILL_LAVA] = StillLava::class;
			Block::$list[Block::SAND] = Sand::class;
			Block::$list[Block::GRAVEL] = Gravel::class;
			Block::$list[Block::GOLD_ORE] = GoldOre::class;
			Block::$list[Block::IRON_ORE] = IronOre::class;
			Block::$list[Block::COAL_ORE] = CoalOre::class;
			Block::$list[Block::WOOD] = Wood::class;
			Block::$list[Block::LEAVES] = Leaves::class;
			Block::$list[Block::SPONGE] = Sponge::class;
			Block::$list[Block::GLASS] = Glass::class;
			Block::$list[Block::LAPIS_ORE] = LapisOre::class;
			Block::$list[Block::LAPIS_BLOCK] = Lapis::class;
			Block::$list[Block::SANDSTONE] = Sandstone::class;
			Block::$list[Block::BED_BLOCK] = Bed::class;
			Block::$list[Block::COBWEB] = Cobweb::class;
			Block::$list[Block::TALL_GRASS] = TallGrass::class;
			Block::$list[Block::DEAD_BUSH] = DeadBush::class;
			Block::$list[Block::WOOL] = Wool::class;
			Block::$list[Block::DANDELION] = Dandelion::class;
			Block::$list[Block::RED_FLOWER] = Flower::class;
			Block::$list[Block::BROWN_MUSHROOM] = BrownMushroom::class;
			Block::$list[Block::RED_MUSHROOM] = RedMushroom::class;
			Block::$list[Block::GOLD_BLOCK] = Gold::class;
			Block::$list[Block::IRON_BLOCK] = Iron::class;
			Block::$list[Block::DOUBLE_SLAB] = DoubleSlab::class;
			Block::$list[Block::SLAB] = Slab::class;
			Block::$list[Block::BRICKS_BLOCK] = Bricks::class;
			Block::$list[Block::TNT] = TNT::class;
			Block::$list[Block::BOOKSHELF] = Bookshelf::class;
			Block::$list[Block::MOSS_STONE] = MossStone::class;
			Block::$list[Block::OBSIDIAN] = Obsidian::class;
			Block::$list[Block::TORCH] = Torch::class;
			Block::$list[Block::FIRE] = Fire::class;
			Block::$list[Block::MONSTER_SPAWNER] = MonsterSpawner::class;
			Block::$list[Block::WOOD_STAIRS] = WoodStairs::class;
			Block::$list[Block::CHEST] = Chest::class;

			Block::$list[Block::DIAMOND_ORE] = DiamondOre::class;
			Block::$list[Block::DIAMOND_BLOCK] = Diamond::class;
			Block::$list[Block::WORKBENCH] = Workbench::class;
			Block::$list[Block::WHEAT_BLOCK] = Wheat::class;
			Block::$list[Block::FARMLAND] = Farmland::class;
			Block::$list[Block::FURNACE] = Furnace::class;
			Block::$list[Block::BURNING_FURNACE] = BurningFurnace::class;
			Block::$list[Block::SIGN_POST] = SignPost::class;
			Block::$list[Block::WOOD_DOOR_BLOCK] = WoodDoor::class;
			Block::$list[Block::SPRUCE_DOOR_BLOCK] = SpruceDoor::class;
			Block::$list[Block::LADDER] = Ladder::class;
			Block::$list[Block::RAIL] = Rail::class;
			Block::$list[Block::COBBLESTONE_STAIRS] = CobblestoneStairs::class;
			Block::$list[Block::WALL_SIGN] = WallSign::class;

			Block::$list[Block::IRON_DOOR_BLOCK] = IronDoor::class;
			Block::$list[Block::REDSTONE_ORE] = RedstoneOre::class;
			Block::$list[Block::GLOWING_REDSTONE_ORE] = GlowingRedstoneOre::class;

			Block::$list[Block::SNOW_LAYER] = SnowLayer::class;
			Block::$list[Block::ICE] = Ice::class;
			Block::$list[Block::SNOW_BLOCK] = Snow::class;
			Block::$list[Block::CACTUS] = Cactus::class;
			Block::$list[Block::CLAY_BLOCK] = Clay::class;
			Block::$list[Block::SUGARCANE_BLOCK] = Sugarcane::class;

			Block::$list[Block::FENCE] = Fence::class;
			Block::$list[Block::PUMPKIN] = Pumpkin::class;
			Block::$list[Block::NETHERRACK] = Netherrack::class;
			Block::$list[Block::SOUL_SAND] = SoulSand::class;
			Block::$list[Block::GLOWSTONE_BLOCK] = Glowstone::class;

			Block::$list[Block::LIT_PUMPKIN] = LitPumpkin::class;
			Block::$list[Block::CAKE_BLOCK] = Cake::class;

			Block::$list[Block::TRAPDOOR] = Trapdoor::class;
			Block::$list[Block::IRON_TRAPDOOR] = IronTrapdoor::class;

			Block::$list[Block::STONE_BRICKS] = StoneBricks::class;

			Block::$list[Block::IRON_BARS] = IronBars::class;
			Block::$list[Block::GLASS_PANE] = GlassPane::class;
			Block::$list[Block::MELON_BLOCK] = Melon::class;
			Block::$list[Block::PUMPKIN_STEM] = PumpkinStem::class;
			Block::$list[Block::MELON_STEM] = MelonStem::class;
			Block::$list[Block::VINE] = Vine::class;
			Block::$list[Block::FENCE_GATE] = FenceGate::class;
			Block::$list[Block::BRICK_STAIRS] = BrickStairs::class;
			Block::$list[Block::STONE_BRICK_STAIRS] = StoneBrickStairs::class;

			Block::$list[Block::MYCELIUM] = Mycelium::class;
			Block::$list[Block::WATER_LILY] = WaterLily::class;
			Block::$list[Block::NETHER_BRICKS] = NetherBrick::class;
			Block::$list[Block::NETHER_BRICK_FENCE] = NetherBrickFence::class;

			Block::$list[Block::NETHER_BRICKS_STAIRS] = NetherBrickStairs::class;

			Block::$list[Block::ENCHANTING_TABLE] = EnchantingTable::class;

			Block::$list[Block::END_PORTAL_FRAME] = EndPortalFrame::class;
			Block::$list[Block::END_STONE] = EndStone::class;
			Block::$list[Block::SANDSTONE_STAIRS] = SandstoneStairs::class;
			Block::$list[Block::EMERALD_ORE] = EmeraldOre::class;

			Block::$list[Block::EMERALD_BLOCK] = Emerald::class;
			Block::$list[Block::SPRUCE_WOOD_STAIRS] = SpruceWoodStairs::class;
			Block::$list[Block::BIRCH_WOOD_STAIRS] = BirchWoodStairs::class;
			Block::$list[Block::JUNGLE_WOOD_STAIRS] = JungleWoodStairs::class;
			Block::$list[Block::STONE_WALL] = StoneWall::class;

			Block::$list[Block::CARROT_BLOCK] = Carrot::class;
			Block::$list[Block::POTATO_BLOCK] = Potato::class;
			Block::$list[Block::ANVIL] = Anvil::class;

			Block::$list[Block::REDSTONE_BLOCK] = Redstone::class;

			Block::$list[Block::QUARTZ_BLOCK] = Quartz::class;
			Block::$list[Block::QUARTZ_STAIRS] = QuartzStairs::class;
			Block::$list[Block::DOUBLE_WOOD_SLAB] = DoubleWoodSlab::class;
			Block::$list[Block::WOOD_SLAB] = WoodSlab::class;
			Block::$list[Block::STAINED_CLAY] = StainedClay::class;

			Block::$list[Block::LEAVES2] = Leaves2::class;
			Block::$list[Block::WOOD2] = Wood2::class;
			Block::$list[Block::ACACIA_WOOD_STAIRS] = AcaciaWoodStairs::class;
			Block::$list[Block::DARK_OAK_WOOD_STAIRS] = DarkOakWoodStairs::class;

			Block::$list[Block::HAY_BALE] = HayBale::class;
			Block::$list[Block::CARPET] = Carpet::class;
			Block::$list[Block::HARDENED_CLAY] = HardenedClay::class;
			Block::$list[Block::COAL_BLOCK] = Coal::class;

			Block::$list[Block::DOUBLE_PLANT] = DoublePlant::class;

			Block::$list[Block::FENCE_GATE_SPRUCE] = FenceGateSpruce::class;
			Block::$list[Block::FENCE_GATE_BIRCH] = FenceGateBirch::class;
			Block::$list[Block::FENCE_GATE_JUNGLE] = FenceGateJungle::class;
			Block::$list[Block::FENCE_GATE_DARK_OAK] = FenceGateDarkOak::class;
			Block::$list[Block::FENCE_GATE_ACACIA] = FenceGateAcacia::class;

			Block::$list[Block::GRASS_PATH] = GrassPath::class;

			Block::$list[Block::PODZOL] = Podzol::class;
			Block::$list[Block::BEETROOT_BLOCK] = Beetroot::class;
			Block::$list[Block::STONECUTTER] = Stonecutter::class;
			Block::$list[Block::GLOWING_OBSIDIAN] = GlowingObsidian::class;
			Block::$list[Block::NETHER_REACTOR] = NetherReactor::class;
			//Block::$list[Block::INVISIBLE_BEDROCK] = InvisibleBedrock::class;
			//Block::$list[Block::CONCRETE] = Concrete::class;
			//Block::$list[Block::CONCRETE_POWDER] = ConcretePowder::class;
			
			Block::$list[Block::SLIME_BLOCK] = SlimeBlock::class;
			
			Block::$list[Block::WOODEN_BUTTON] = WoodenButton::class;
			Block::$list[Block::STONE_BUTTON] = StoneButton::class;
			
			Block::$list[Block::ACACIA_DOOR_BLOCK] = AcaciaDoor::class;
			Block::$list[Block::BIRCH_DOOR_BLOCK] = BirchDoor::class;
			Block::$list[Block::DARK_OAK_DOOR_BLOCK] = DarkOakDoor::class;
			Block::$list[Block::JUNGLE_DOOR_BLOCK] = JungleDoor::class;
			
			Block::$list[Block::TRIPWIRE] = Tripwire::class;
			Block::$list[Block::TRIPWIRE_HOOK] = TripwireHook::class;
			
			Block::$list[Block::LEVER] = Lever::class;
			
			Block::$list[Block::WOODEN_PRESSURE_PLATE] = WoodenPressurePlate::class;
			Block::$list[Block::STONE_PRESSURE_PLATE] = StonePressurePlate::class;
			
			Block::$list[Block::REDSTONE_WIRE] = RedstoneWire::class;
			Block::$list[Block::REDSTONE_REPEATER_BLOCK] = RedstoneRepeater::class;
			Block::$list[Block::REDSTONE_REPEATER_BLOCK_ACTIVE] = RedstoneRepeaterActive::class;
			
			Block::$list[Block::POWERED_RAIL] = PoweredRail::class;
			Block::$list[Block::DETECTOR_RAIL] = DetectorRail::class;
			Block::$list[Block::ACTIVATOR_RAIL] = ActivatorRail::class;
			
			Block::$list[Block::WEIGHTED_PRESSURE_PLATE_HEAVY] = WeightedPressurePlateHeavy::class;
			Block::$list[Block::WEIGHTED_PRESSURE_PLATE_LIGHT] = WeightedPressurePlateLight::class;
			
			Block::$list[Block::MOB_HEAD_BLOCK] = MobHead::class;
			Block::$list[Block::FLOWER_POT_BLOCK] = FlowerPot::class;
			
			Block::$list[Block::CHORUS_FLOWER] = ChorusFlower::class;
			Block::$list[Block::CHORUS_PLANT] = ChorusPlant::class;
			Block::$list[Block::ENDER_CHEST] = EnderChest::class;
			Block::$list[Block::END_GATEWAY] = EndGateway::class;
			Block::$list[Block::END_PORTAL] = EndPortal::class;
			Block::$list[Block::END_BRICKS] = EndBricks::class;
			Block::$list[Block::END_ROD] = EndRod::class;
			Block::$list[Block::DRAGON_EGG] = DragonEgg::class;
			Block::$list[Block::PURPUR_BLOCK] = PurpurBlock::class;
			Block::$list[Block::STAINED_GLASS] = StainedGlass::class;
			Block::$list[Block::STAINED_GLASS_PANE] = StainedGlassPane::class;

			Block::$list[Block::REDSTONE_LAMP] = RedstoneLamp::class;
			Block::$list[Block::REDSTONE_LAMP_ACTIVE] = RedstoneLampActive::class;
			//Block::$list[Block::LIT_REDSTONE_LAMP] = LitRedstoneLamp::class;
			//Block::$list[Block::POWERED_REPEATER_BLOCK] = PoweredRepeater::class;
			//Block::$list[Block::UNPOWERED_REPEATER_BLOCK] = UnpoweredRepeater::class;
			
			Block::$list[Block::REDSTONE_TORCH] = RedstoneTorch::class;
			Block::$list[Block::REDSTONE_TORCH_ACTIVE] = RedstoneTorchActive::class;
            
			foreach(Block::$list as $id => $class){
				if($class !== null){
					$block = new $class();

					for($data = 0; $data < 16; ++$data){
						Block::$fullList[($id << 4) | $data] = new $class($data);
					}

					Block::$solid[$id] = $block->isSolid();
					Block::$transparent[$id] = $block->isTransparent();
					Block::$hardness[$id] = $block->getHardness();
					Block::$light[$id] = $block->getLightLevel();

					if($block->isSolid()){
						if($block->isTransparent()){
							if($block instanceof Liquid or $block instanceof Ice){
								Block::$lightFilter[$id] = 2;
							}else{
								Block::$lightFilter[$id] = 1;
							}
						}else{
							Block::$lightFilter[$id] = 15;
						}
					}else{
						Block::$lightFilter[$id] = 1;
					}
				}else{
					Block::$lightFilter[$id] = 1;
					for($data = 0; $data < 16; ++$data){
						Block::$fullList[($id << 4) | $data] = new Block($id, $data);
					}
				}
			}
		}
	}

	/**
	 * @param int      $id
	 * @param int      $meta
	 * @param Position $pos
	 *
	 * @return Block
	 */
	public static function get($id, $meta = 0, Position $pos = null){
		try{
			$block = Block::$list[$id];
			if($block !== null){
				$block = new $block($meta);
			}else{
				$block = new Block($id, $meta);
			}
		}catch(\RuntimeException $e){
			$block = new Block($id, $meta);
		}

		if($pos !== null){
			$block->x = $pos->x;
			$block->y = $pos->y;
			$block->z = $pos->z;
			$block->level = $pos->level;
		}

		return $block;
	}

	/**
	 * @param int $id
	 * @param int $meta
	 */
	public function __construct($id, $meta = 0){
		$this->id = (int) $id;
		$this->meta = (int) $meta;
	}

	/**
	 * @param Item   $item
	 * @param Block  $block
	 * @param Block  $target
	 * @param int    $face
	 * @param float  $fx
	 * @param float  $fy
	 * @param float  $fz
	 * @param Player $player = null
	 *
	 * @return bool
	 */
	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		return $this->getLevel()->setBlock($this, $this, true, true);
	}

	/**
	 * @param Item $item
	 *
	 * @return bool
	 */
	public function isBreakable(Item $item){
		return true;
	}

	/**
	 * @param Item $item
	 *
	 * @return mixed
	 */
	public function onBreak(Item $item){
		return $this->getLevel()->setBlock($this, new Air(), true, true);
	}

	/**
	 * @param int $type
	 *
	 * @return void
	 */
	public function onUpdate($type){

	}

	/**
	 * @param Item   $item
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function onActivate(Item $item, Player $player = null){
		return false;
	}

	/**
	 * @return int
	 */
	public function getHardness(){
		return 10;
	}

	/**
	 * @return int
	 */
	public function getResistance(){
		return $this->getHardness() * 5;
	}

	/**
	 * @return int
	 */
	public function getToolType(){
		return Tool::TYPE_NONE;
	}

	/**
	 * @return float
	 */
	public function getFrictionFactor(){
		return 0.6;
	}

	/**
	 * @return int 0-15
	 */
	public function getLightLevel(){
		return 0;
	}

	/**
	 * AKA: Block->isPlaceable
	 *
	 * @return bool
	 */
	public function canBePlaced(){
		return true;
	}

	/**
	 * AKA: Block->canBeReplaced()
	 *
	 * @return bool
	 */
	public function canBeReplaced(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function isTransparent(){
		return false;
	}

	public function isSolid(){
		return true;
	}

	/**
	 * @return bool
	 */
	public function canBeFlowedInto(){
		return false;
	}

	/**
	 * @return bool
	 */
	public function canBeActivated(){
		return false;
	}

	public function hasEntityCollision(){
		return false;
	}

	public function canPassThrough(){
		return false;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return "Bilinmeyen";
	}

	/**
	 * @return int
	 */
	final public function getId(){
		return $this->id;
	}

	public function addVelocityToEntity(Entity $entity, Vector3 $vector){

	}

	/**
	 * @return int
	 */
	final public function getDamage(){
		return $this->meta;
	}

	/**
	 * @param int $meta
	 */
	final public function setDamage($meta){
		$this->meta = $meta & 0x0f;
	}

	/**
	 * @param Position $v
	 */
	final public function position(Position $v){
		$this->x = (int) $v->x;
		$this->y = (int) $v->y;
		$this->z = (int) $v->z;
		$this->level = $v->level;
		$this->boundingBox = null;
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item){
		if(!isset(Block::$list[$this->getId()])){
			return [];
		}else{
			return [
				[$this->getId(), $this->getDamage(), 1],
			];
		}
	}

	/** 
	 * @param Item $item
	 *
	 * @return float
	 */
	public function getBreakTime(Item $item) {
		static $tierMultipliers = [
			Tool::TIER_WOODEN => 2,
			Tool::TIER_STONE => 4,
			Tool::TIER_IRON => 6,
			Tool::TIER_DIAMOND => 8,
			Tool::TIER_GOLD => 12,
		];
		
		if (!$this->canBeBrokenWith($item)) {
			return -1;
		}
		$toolType = $this->getToolType();
		$isSuitableForHarvest = !empty($this->getDrops($item)) || $toolType == Tool::TYPE_NONE;
		$secondsForBreak = $this->getHardness() * ($isSuitableForHarvest ? 1.5 : 5);
		if ($secondsForBreak == 0) {
			$secondsForBreak = 0.05;
		}
		
		switch ($toolType) {
			case Tool::TYPE_SWORD:
				if ($item->isSword()) {
					if ($this->id == Block::COBWEB) {
						$secondsForBreak = $secondsForBreak / 15;
					}
					return $secondsForBreak;
				}
				break;
			case Tool::TYPE_SHEARS:
				if ($item->isShears()) {
					if ($this->id == Block::WOOL) {
						$secondsForBreak = $secondsForBreak / 5;
					} else {
						$secondsForBreak = $secondsForBreak / 15;
					}
					return $secondsForBreak;
				}
				break;
			case Tool::TYPE_SHOVEL:
				$tier = $item->isShovel();
				if ($tier !== false && isset($tierMultipliers[$tier])) {
					return $secondsForBreak / $tierMultipliers[$tier];
				}
				break;
			case Tool::TYPE_PICKAXE:
				$tier = $item->isPickaxe();
				if ($tier !== false && isset($tierMultipliers[$tier])) {
					return $secondsForBreak / $tierMultipliers[$tier];
				}
				break;
			case Tool::TYPE_AXE:
				$tier = $item->isAxe();
				if ($tier !== false && isset($tierMultipliers[$tier])) {
					return $secondsForBreak / $tierMultipliers[$tier];
				}
				break;
		}
		
		return $secondsForBreak;
	}

	public function canBeBrokenWith(Item $item){
		return $this->getHardness() !== -1;
	}

	/**
	 * @param int $side
	 * @param int $step
	 *
	 * @return Block
	 */
	public function getSide($side, $step = 1){
		if($this->isValid()){
			return $this->getLevel()->getBlock(Vector3::getSide($side, $step));
		}
		return Block::get(Item::AIR, 0, Position::fromObject(Vector3::getSide($side, $step)));
	}

	/**
	 * @return string
	 */
	public function __toString(){
		return "Block[" . $this->getName() . "] (" . $this->getId() . ":" . $this->getDamage() . ")";
	}

	/**
	 * @param AxisAlignedBB $bb
	 *
	 * @return bool
	 */
	public function collidesWithBB(AxisAlignedBB $bb){
		$bb2 = $this->getBoundingBox();

		return $bb2 !== null and $bb->intersectsWith($bb2);
	}

	/**
	 * @param Entity $entity
	 */
	public function onEntityCollide(Entity $entity){

	}

	/**
	 * @return AxisAlignedBB
	 */
	public function getBoundingBox(){
		if($this->boundingBox === null){
			$this->boundingBox = $this->recalculateBoundingBox();
		}
		return $this->boundingBox;
	}

	/**
	 * @return AxisAlignedBB
	 */
	protected function recalculateBoundingBox(){
		return new AxisAlignedBB(
			$this->x,
			$this->y,
			$this->z,
			$this->x + 1,
			$this->y + 1,
			$this->z + 1
		);
	}

	/**
	 * @param Vector3 $pos1
	 * @param Vector3 $pos2
	 *
	 * @return MovingObjectPosition
	 */
	public function calculateIntercept(Vector3 $pos1, Vector3 $pos2){
		$bb = $this->getBoundingBox();
		if($bb === null){
			return null;
		}

		$v1 = $pos1->getIntermediateWithXValue($pos2, $bb->minX);
		$v2 = $pos1->getIntermediateWithXValue($pos2, $bb->maxX);
		$v3 = $pos1->getIntermediateWithYValue($pos2, $bb->minY);
		$v4 = $pos1->getIntermediateWithYValue($pos2, $bb->maxY);
		$v5 = $pos1->getIntermediateWithZValue($pos2, $bb->minZ);
		$v6 = $pos1->getIntermediateWithZValue($pos2, $bb->maxZ);

		if($v1 !== null and !$bb->isVectorInYZ($v1)){
			$v1 = null;
		}

		if($v2 !== null and !$bb->isVectorInYZ($v2)){
			$v2 = null;
		}

		if($v3 !== null and !$bb->isVectorInXZ($v3)){
			$v3 = null;
		}

		if($v4 !== null and !$bb->isVectorInXZ($v4)){
			$v4 = null;
		}

		if($v5 !== null and !$bb->isVectorInXY($v5)){
			$v5 = null;
		}

		if($v6 !== null and !$bb->isVectorInXY($v6)){
			$v6 = null;
		}

		$vector = $v1;

		if($v2 !== null and ($vector === null or $pos1->distanceSquared($v2) < $pos1->distanceSquared($vector))){
			$vector = $v2;
		}

		if($v3 !== null and ($vector === null or $pos1->distanceSquared($v3) < $pos1->distanceSquared($vector))){
			$vector = $v3;
		}

		if($v4 !== null and ($vector === null or $pos1->distanceSquared($v4) < $pos1->distanceSquared($vector))){
			$vector = $v4;
		}

		if($v5 !== null and ($vector === null or $pos1->distanceSquared($v5) < $pos1->distanceSquared($vector))){
			$vector = $v5;
		}

		if($v6 !== null and ($vector === null or $pos1->distanceSquared($v6) < $pos1->distanceSquared($vector))){
			$vector = $v6;
		}

		if($vector === null){
			return null;
		}

		$f = -1;

		if($vector === $v1){
			$f = 4;
		}elseif($vector === $v2){
			$f = 5;
		}elseif($vector === $v3){
			$f = 0;
		}elseif($vector === $v4){
			$f = 1;
		}elseif($vector === $v5){
			$f = 2;
		}elseif($vector === $v6){
			$f = 3;
		}

		return MovingObjectPosition::fromBlock($this->x, $this->y, $this->z, $f, $vector->add($this->x, $this->y, $this->z));
	}

	public function setMetadata($metadataKey, MetadataValue $metadataValue){
		if($this->getLevel() instanceof Level){
			$this->getLevel()->getBlockMetadata()->setMetadata($this, $metadataKey, $metadataValue);
		}
	}

	public function getMetadata($metadataKey){
		if($this->getLevel() instanceof Level){
			return $this->getLevel()->getBlockMetadata()->getMetadata($this, $metadataKey);
		}

		return null;
	}

	public function hasMetadata($metadataKey){
		if($this->getLevel() instanceof Level){
			$this->getLevel()->getBlockMetadata()->hasMetadata($this, $metadataKey);
		}
	}

	public function removeMetadata($metadataKey, Plugin $plugin){
		if($this->getLevel() instanceof Level){
			$this->getLevel()->getBlockMetadata()->removeMetadata($this, $metadataKey, $plugin);
		}
	}
}
