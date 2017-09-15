<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\inventory;

use pocketmine\item\Item;
use pocketmine\Player;

interface Inventory{
	
	const MAX_STACK = 64;

	public function getSize();

	public function getMaxStackSize();

	/**
	 * @param int $size
	 */
	public function setMaxStackSize($size);

	public function getName();

	public function getTitle();

	/**
	 * @param int $index
	 *
	 * @return Item
	 */
	public function getItem($index);

	/**
	 * @param int    $index
	 * @param Item   $item
	 *
	 * @return bool
	 */
	public function setItem($index, Item $item);

	/**
	 * @param Item ...$item
	 *
	 * @return Item[]
	 */
	public function addItem(...$slots);

	/**
	 * @param Item $item
	 *
	 * @return bool
	 */
	public function canAddItem(Item $item);

	/**
	 * @param Item ...$item
	 *
	 * @return Item[]
	 */
	public function removeItem(...$slots);

	/**
	 * @return Item[]
	 */
	public function getContents();

	/**
	 * @param Item[] $items
	 */
	public function setContents(array $items);

	/**
	 * @param Player|Player[] $target
	 */
	public function sendContents($target);

	/**
	 * @param int             $index
	 * @param Player|Player[] $target
	 */
	public function sendSlot($index, $target);

	/**
	 * @param Item $item
	 *
	 * @return bool
	 */
	public function contains(Item $item);

	/**
	 * @param Item $item
	 *
	 * @return Item[]
	 */
	public function all(Item $item);

	/**
	 * @param Item $item
	 *
	 * @return int
	 */
	public function first(Item $item);

	/**
	 * @return int
	 */
	public function firstEmpty();

	/**
	 * @param Item $item
	 */
	public function remove(Item $item);

	/**
	 * @param int    $index
	 *
	 * @return bool
	 */
	public function clear($index);

	/**
	 * Clears all the slots
	 */
	public function clearAll();

	/**
	 * @return Player[]
	 */
	public function getViewers();

	/**
	 * @return InventoryType
	 */
	public function getType();

	/**
	 * @return InventoryHolder
	 */
	public function getHolder();

	/**
	 * @param Player $who
	 */
	public function onOpen(Player $who);

	/**
	 * @param Player $who
	 *
	 * @return bool
	 */
	public function open(Player $who);

	public function close(Player $who);

	/**
	 * @param Player $who
	 */
	public function onClose(Player $who);

	/**
	 * @param int    $index
	 * @param Item   $before
	 */
	public function onSlotChange($index, $before, $sendPacket = true);
	
}
