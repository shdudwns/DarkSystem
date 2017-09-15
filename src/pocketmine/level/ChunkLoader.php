<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\level;

use pocketmine\block\Block;
use pocketmine\level\format\Chunk;
use pocketmine\math\Vector3;

interface ChunkLoader{

	/**
	 * @return int
	 */
	public function getLoaderId();

	/**
	 * @return bool
	 */
	public function isLoaderActive();

	/**
	 * @return Position
	 */
	public function getPosition();

	/**
	 * @return float
	 */
	public function getX();

	/**
	 * @return float
	 */
	public function getZ();

	/**
	 * @return Level
	 */
	public function getLevel();

	/**
	 * @param Chunk $chunk
	 */
	public function onChunkChanged(Chunk $chunk);

	/**
	 * @param Chunk $chunk
	 */
	public function onChunkLoaded(Chunk $chunk);


	/**
	 * @param Chunk $chunk
	 */
	public function onChunkUnloaded(Chunk $chunk);

	/**
	 * @param Chunk $chunk
	 */
	public function onChunkPopulated(Chunk $chunk);

	/**
	 * @param Block|Vector3 $block
	 */
	public function onBlockChanged(Vector3 $block);

}