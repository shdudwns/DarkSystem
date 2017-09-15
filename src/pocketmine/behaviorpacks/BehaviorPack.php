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

interface BehaviorPack{

	/**
	 * Returns the human-readable name of the behavior pack
	 * @return string
	 */
	public function getPackName() : string;

	/**
	 * Returns the pack's UUID as a human-readable string
	 * @return string
	 */
	public function getPackId() : string;

	/**
	 * Returns the size of the pack on disk in bytes.
	 * @return int
	 */
	public function getPackSize() : int;

	/**
	 * Returns a version number for the pack in the format major.minor.patch
	 * @return string
	 */
	public function getPackVersion() : string;

	/**
	 * Returns the raw SHA256 sum of the compressed behavior pack zip. This is used by clients to validate pack downloads.
	 * @return string byte-array length 32 bytes
	 */
	public function getSha256() : string;

	/**
	 * Returns a chunk of the behavior pack zip as a byte-array for sending to clients.
	 *
	 * Note that behavior packs must **always** be in zip archive format for sending.
	 * A folder behavior loader may need to perform on-the-fly compression for this purpose.
	 *
	 * @param int $start Offset to start reading the chunk from
	 * @param int $length Maximum length of data to return.
	 *
	 * @return string byte-array
	 */
	public function getPackChunk(int $start, int $length) : string;
}