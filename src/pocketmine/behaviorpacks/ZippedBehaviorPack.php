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

class ZippedBehaviorPack implements BehaviorPack{

	/**
	 * @param \stdClass $manifest
	 *
	 * @return bool
	 */
	public static function verifyManifest(\stdClass $manifest){
		if(!isset($manifest->format_version) or !isset($manifest->header) or !isset($manifest->modules)){
			return false;
		}
		return
			isset($manifest->header->description) and
			isset($manifest->header->name) and
			isset($manifest->header->uuid) and
			isset($manifest->header->version) and
			count($manifest->header->version) === 3;
	}

	/** @var string */
	protected $path;

	/** @var \stdClass */
	protected $manifest;

	/** @var string */
	protected $sha256 = null;

	/** @var resource */
	protected $fileResource;

	/**
	 * @param string $zipPath Path to the behavior pack zip
	 */
	public function __construct(string $zipPath){
		$this->path = $zipPath;

		if(!file_exists($zipPath)){
			throw new \InvalidArgumentException("Could not open behavior pack $zipPath: file not found");
		}

		$archive = new \ZipArchive();
		if(($openResult = $archive->open($zipPath)) !== true){
			throw new \InvalidStateException("Encountered ZipArchive error code $openResult while trying to open $zipPath");
		}

		if(($manifestData = $archive->getFromName("pack_manifest.json")) === false){
			throw new \InvalidStateException("Could not load behavior pack from $zipPath: pack_manifest.json not found");
		}

		$archive->close();

		$manifest = json_decode($manifestData);
		if(!self::verifyManifest($manifest)){
			throw new \InvalidStateException("Could not load behavior pack from $zipPath: pack_manifest.json is invalid or incomplete");
		}

		$this->manifest = $manifest;

		$this->fileResource = fopen($zipPath, "rb");
	}

	public function __destruct(){
		fclose($this->fileResource);
	}

	public function getPackName() : string{
		return $this->manifest->header->name;
	}

	public function getPackVersion() : string{
		return implode(".", $this->manifest->header->version);
	}

	public function getPackId() : string{
		return $this->manifest->header->uuid;
	}

	public function getPackSize() : int{
		return filesize($this->path);
	}

	public function getSha256(bool $cached = true) : string{
		if($this->sha256 === null or !$cached){
			$this->sha256 = hash_file("sha256", $this->path, true);
		}
		return $this->sha256;
	}

	public function getPackChunk(int $start, int $length) : string{
		fseek($this->fileResource, $start);
		if(feof($this->fileResource)){
			throw new \RuntimeException("Requested a behavior pack chunk with invalid start offset");
		}
		
		return fread($this->fileResource, $length);
	}
}