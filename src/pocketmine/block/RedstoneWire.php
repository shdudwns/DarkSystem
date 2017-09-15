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

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class RedstoneWire extends RedstoneSource{

	const ON = 1;
	const OFF = 2;
	const PLACE = 3;
	const DESTROY = 4;

	protected $id = self::REDSTONE_WIRE;

	/**
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return "Redstone Wire";
	}

	/**
	 * @return int
	 */
	public function getStrength(){
		return $this->meta;
	}

	/**
	 * @param Block|null $from
	 *
	 * @return bool
	 */
	public function isActivated(Block $from = null){
		return ($this->meta > 0);
	}

	/**
	 * @return int
	 */
	public function getHighestStrengthAround(){
		$strength = 0;
		$hasChecked = [
			Vector3::SIDE_WEST => false,
			Vector3::SIDE_EAST => false,
			Vector3::SIDE_NORTH => false,
			Vector3::SIDE_SOUTH => false
		];
		foreach($hasChecked as $side => $bool){
			$block = $this->getSide($side);
			if($block instanceof RedstoneSource){
				if(($block->getStrength() > $strength) and $block->isActivated($this)) $strength = $block->getStrength();
				$hasChecked[$side] = true;
			}
		}
		
		$baseBlock = $this->add(0, 1, 0);
		foreach($hasChecked as $side => $bool){
			if(!$bool){
				$block = $this->getLevel()->getBlock($baseBlock->getSide($side));
				if($block->getId() == $this->id){
					if($block->getStrength() > $strength) $strength = $block->getStrength();
					$hasChecked[$side] = true;
				}
			}
		}
		
		$baseBlock = $this->add(0, -1, 0);
		foreach($hasChecked as $side => $bool){
			if(!$bool){
				$block = $this->getLevel()->getBlock($baseBlock->getSide($side));
				if($block->getId() == $this->id){
					if($block->getStrength() > $strength) $strength = $block->getStrength();
					$hasChecked[$side] = true;
				}
			}
		}

		unset($block);
		unset($hasChecked);

		return $strength;
	}

	/**
	 * @return array
	 */
	public function getConnectedWires(){
		$hasChecked = [
			Vector3::SIDE_WEST => false,
			Vector3::SIDE_EAST => false,
			Vector3::SIDE_NORTH => false,
			Vector3::SIDE_SOUTH => false
		];
		foreach($hasChecked as $side => $bool){
			$block = $this->getSide($side);
			if($block instanceof RedstoneSource and !$block instanceof PoweredRepeater){
				$hasChecked[$side] = true;
			}
			
			if($block instanceof PoweredRepeater){
				if($this->equals($block->getSide($block->getOppositeDirection()))){
					$hasChecked[$side] = true;
				}
			}
		}
		
		$baseBlock = $this->add(0, 1, 0);
		foreach($hasChecked as $side => $bool){
			if(!$bool){
				$block = $this->getLevel()->getBlock($baseBlock->getSide($side));
				if($block->getId() == $this->id){
					$hasChecked[$side] = true;
				}
			}
		}
		
		$baseBlock = $this->add(0, -1, 0);
		foreach($hasChecked as $side => $bool){
			if(!$bool){
				$block = $this->getLevel()->getBlock($baseBlock->getSide($side));
				if($block->getId() == $this->id){
					$hasChecked[$side] = true;
				}
			}
		}

		unset($block);

		return $hasChecked;
	}

	/**
	 * @return array
	 */
	public function getUnconnectedSide(){
		$connected = [];
		$notConnected = [];

		foreach($this->getConnectedWires() as $key => $bool){
			if($bool){
				$connected[] = $key;
			}else $notConnected[] = $key;
		}

		if(count($connected) == 1){
			return [static::getOppositeSide($connected[0]), $connected];
		}elseif(count($connected) == 3){
			return [$notConnected[0], $connected];
		}else return [false, $connected];
	}

	/**
	 * @param array $ignore
	 *
	 * @return bool|void
	 */
	public function activate(array $ignore = []){
		if($this->canCalc()){
			$block = $this->getSide(Vector3::SIDE_DOWN);
			if($block->getId() == Block::REDSTONE_LAMP or $block->getId() == Block::REDSTONE_LAMP_ACTIVE) $block->turnOn();

			$side = $this->getUnconnectedSide();

			$sides = [Vector3::SIDE_WEST, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_NORTH];
			foreach($sides as $s){
				if(!in_array($s, $side[1])){
					$block = $this->getSide(Vector3::SIDE_DOWN)->getSide($s);
					$this->activateBlock($block);
				}
			}

			if($side[0] == false) return;
			$block = $this->getSide($side[0]);
			$this->activateBlock($block);

			if(!$block->isTransparent()){
				$sides = [Vector3::SIDE_WEST, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_NORTH, Vector3::SIDE_UP, Vector3::SIDE_DOWN];
				foreach($sides as $s){
					if($s != static::getOppositeSide($side[0])){
						$this->activateBlockWithoutWire($block->getSide($s));
					}
				}
			}

			$this->checkTorchOn($block, [static::getOppositeSide($side)]);

			unset($connected, $notConnected);
		}
	}

	/**
	 * @param array $ignore
	 *
	 * @return bool|void
	 */
	public function deactivate(array $ignore = []){
		if($this->canCalc()){
			$block = $this->getSide(Vector3::SIDE_DOWN);
			if($block->getId() == Block::REDSTONE_LAMP_ACTIVE){
				if(!$this->checkPower($block, [Vector3::SIDE_UP], true)) $block->turnOff();
			}

			$side = $this->getUnconnectedSide();

			$sides = [Vector3::SIDE_WEST, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_NORTH];
			foreach($sides as $s){
				if(!in_array($s, $side[1])){
					$this->deactivateBlock($this->getSide(Vector3::SIDE_DOWN)->getSide($s));
				}
			}

			if($side[0] == false) return;
			$block = $this->getSide($side[0]);
			$this->deactivateBlockWithoutWire($block);

			if(!$block->isTransparent()){
				$sides = [Vector3::SIDE_WEST, Vector3::SIDE_EAST, Vector3::SIDE_SOUTH, Vector3::SIDE_NORTH, Vector3::SIDE_UP, Vector3::SIDE_DOWN];
				foreach($sides as $s){
					if($s != static::getOppositeSide($side[0])){
						$this->deactivateBlockWithoutWire($block->getSide($s));
					}
				}
			}

			$this->checkTorchOff($block, [static::getOppositeSide($side)]);

			unset($connected, $notConnected);
		}
	}

	/**
	 * @param RedstoneWire $wire
	 * @param array        $powers
	 * @param array        $hasUpdated
	 * @param bool         $isStart
	 *
	 * @return array
	 */
	public function getPowerSources(RedstoneWire $wire, array $powers = [], array $hasUpdated = [], $isStart = false){
		if(!$isStart){
			$wire->meta = 0;
			$wire->getLevel()->setBlock($wire, $wire, true, false);
			$wire->deactivate();
		}
		
		$hasChecked = [
			Vector3::SIDE_WEST => false,
			Vector3::SIDE_EAST => false,
			Vector3::SIDE_NORTH => false,
			Vector3::SIDE_SOUTH => false
		];
		$hash = Level::blockHash($wire->x, $wire->y, $wire->z);
		if(!isset($hasUpdated[$hash])) $hasUpdated[$hash] = true;
		else return [$powers, $hasUpdated];
		foreach($hasChecked as $side => $bool){
			$block = $wire->getSide($side);
			if($block instanceof RedstoneSource){
				if($block->isActivated($wire)){
					if($block->getId() != $this->id){
						$powers[] = $block;
					}else{
						$result = $this->getPowerSources($block, $powers, $hasUpdated);
						$powers = $result[0];
						$hasUpdated = $result[1];
					}
					$hasChecked[$side] = true;
				}
			}
		}
		
		$baseBlock = $wire->add(0, 1, 0);
		foreach($hasChecked as $side => $bool){
			if(!$bool){
				$block = $this->getLevel()->getBlock($baseBlock->getSide($side));
				if($block instanceof RedstoneSource){
					if($block->isActivated($wire)){
						if($block->getId() == $this->id){
							$result = $this->getPowerSources($block, $powers, $hasUpdated);
							$powers = $result[0];
							$hasUpdated = $result[1];
							$hasChecked[$side] = true;
						}
					}
				}
			}
		}
		
		$baseBlock = $wire->add(0, -1, 0);
		foreach($hasChecked as $side => $bool){
			if(!$bool){
				$block = $this->getLevel()->getBlock($baseBlock->getSide($side));
				if($block instanceof RedstoneSource){
					if($block->isActivated($wire)){
						if($block->getId() == $this->id){
							$result = $this->getPowerSources($block, $powers, $hasUpdated);
							$powers = $result[0];
							$hasUpdated = $result[1];
							$hasChecked[$side] = true;
						}
					}
				}
			}
		}

		return [$powers, $hasUpdated];
	}

	/**
	 * @param int   $strength
	 * @param int   $type
	 * @param array $hasUpdated
	 *
	 * @return array
	 */
	public function calcSignal($strength = 15, $type = self::ON, array $hasUpdated = []){
		$hash = Level::blockHash($this->x, $this->y, $this->z);
		if(!in_array($hash, $hasUpdated)){
			$hasUpdated[] = $hash;
			if($type == self::DESTROY or $type == self::OFF){
				$this->meta = $strength;
				$this->getLevel()->setBlock($this, $this, true, false);
				if($type == self::DESTROY) $this->getLevel()->setBlock($this, new Air(), true, false);
				if($strength <= 0) $this->deactivate();
				$powers = $this->getPowerSources($this, [], [], true);
				foreach($powers[0] as $power){
					$power->activate();
				}
			}else{
				if($strength <= 0) return $hasUpdated;
				if($type == self::PLACE) $strength = $this->getHighestStrengthAround() - 1;
				if($type == self::ON) $type = self::PLACE;
				if($this->getStrength() < $strength){
					$this->meta = $strength;
					$this->getLevel()->setBlock($this, $this, true, false);
					$this->activate();

					$hasChecked = [
						Vector3::SIDE_WEST => false,
						Vector3::SIDE_EAST => false,
						Vector3::SIDE_NORTH => false,
						Vector3::SIDE_SOUTH => false
					];

					foreach($hasChecked as $side => $bool){
						$needUpdate = $this->getSide($side);
						if(!in_array(Level::blockHash($needUpdate->x, $needUpdate->y, $needUpdate->z), $hasUpdated)){
							$result = $this->updateNormalWire($needUpdate, $strength - 1, $type, $hasUpdated);
							if(count($result) != count($hasUpdated)){
								$hasUpdated = $result;
								$hasChecked[$side] = true;
							}
						}
					}

					$baseBlock = $this->add(0, 1, 0);
					foreach($hasChecked as $side => $bool){
						if(!$bool){
							$needUpdate = $this->getLevel()->getBlock($baseBlock->getSide($side));
							if(!in_array(Level::blockHash($needUpdate->x, $needUpdate->y, $needUpdate->z), $hasUpdated)){
								$result = $this->updateNormalWire($needUpdate, $strength - 1, $type, $hasUpdated);
								if(count($result) != count($hasUpdated)){
									$hasUpdated = $result;
									$hasChecked[$side] = true;
								}
							}
						}
					}

					$baseBlock = $this->add(0, -1, 0);
					foreach($hasChecked as $side => $bool){
						if(!$bool){
							$needUpdate = $this->getLevel()->getBlock($baseBlock->getSide($side));
							if(!in_array(Level::blockHash($needUpdate->x, $needUpdate->y, $needUpdate->z), $hasUpdated)){
								$result = $this->updateNormalWire($needUpdate, $strength - 1, $type, $hasUpdated);
								if(count($result) != count($hasUpdated)){
									$hasUpdated = $result;
									$hasChecked[$side] = true;
								}
							}
						}
					}
				}
			}
		}
		
		return $hasUpdated;
	}

	/**
	 * @param Block $block
	 * @param       $strength
	 * @param       $type
	 * @param array $hasUpdated
	 *
	 * @return array
	 */
	public function updateNormalWire(Block $block, $strength, $type, array $hasUpdated){
		if($block->getId() == Block::REDSTONE_WIRE){
			if($block->getStrength() < $strength){
				return $block->calcSignal($strength, $type, $hasUpdated);
			}
		}
		
		return $hasUpdated;
	}

	/**
	 * @param int $type
	 *
	 * @return bool|int
	 */
	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$down = $this->getSide(Vector3::SIDE_DOWN);
			if($down instanceof Transparent and $down->getId() != Block::REDSTONE_LAMP and $down->getId() != Block::REDSTONE_LAMP_ACTIVE){
				$this->getLevel()->useBreakOn($this);
				return Level::BLOCK_UPDATE_NORMAL;
			}
		}
		
		return true;
	}

	/**
	 * @param Item        $item
	 * @param Block       $block
	 * @param Block       $target
	 * @param int         $face
	 * @param float       $fx
	 * @param float       $fy
	 * @param float       $fz
	 * @param Player|null $player
	 *
	 * @return bool|void
	 */
	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$down = $this->getSide(Vector3::SIDE_DOWN);
		if($down instanceof Transparent and $down->getId() != Block::REDSTONE_LAMP and $down->getId() != Block::REDSTONE_LAMP_ACTIVE) return;
		else{
			$this->getLevel()->setBlock($block, $this, true, false);
			$this->calcSignal(15, self::PLACE);
		}
	}

	/**
	 * @param Item $item
	 *
	 * @return mixed|void
	 */
	public function onBreak(Item $item){
		if($this->canCalc()) $this->calcSignal(0, self::DESTROY);
		else $this->getLevel()->setBlock($this, new Air());
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item){
		return [
			[Item::REDSTONE, 0, 1]
		];
	}
}