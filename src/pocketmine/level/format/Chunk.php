<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\level\format;

interface Chunk extends FullChunk{
	const SECTION_COUNT = 8;

	/**
	 * @param $fY 0-7, (Y / 16)
	 *
	 * @return bool
	 */
	public function isSectionEmpty($fY);

	/**
	 * @param int $fY 0-7
	 *
	 * @return ChunkSection
	 */
	public function getSection($fY);

	/**
	 * @param int          $fY 0-7
	 * @param ChunkSection $section
	 *
	 * @return boolean
	 */
	public function setSection($fY, ChunkSection $section);

	/**
	 * @return ChunkSection[]
	 */
	public function getSections();

	/**
	 * @param int           $chunkX
	 * @param int           $chunkZ
	 * @param LevelProvider $provider
	 *
	 * @return FullChunk
	 */
	public static function getEmptyChunk($chunkX, $chunkZ, LevelProvider $provider = null);
}