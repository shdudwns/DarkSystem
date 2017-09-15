<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\setup; 

class SetupLanguage{
	
	public static $languages = [
		"tur" => "Türkçe",
		"eng" => "English",
		"chs" => "简体中文",
		"zho" => "繁體中文",
		"jpn" => "日本語",
		"rus" => "Русский",
		"ita" => "Italiano"
	];
	
	private $texts = [];
	private $lang;
	private $langfile;

	public function __construct($lang = ""){
		if(file_exists(\pocketmine\PATH . "src/pocketmine/language/setup/" . $lang . ".ini")){
			$this->lang = $lang;
			$this->langfile = \pocketmine\PATH . "src/pocketmine/language/setup/" . $lang . ".ini";
		}else{
			$files = [];
			foreach(new \DirectoryIterator(\pocketmine\PATH . "src/pocketmine/language/setup/") as $file){
				if($file->getExtension() === "ini" and substr($file->getFilename(), 0, 2) === $lang){
					$files[$file->getFilename()] = $file->getSize();
				}
			}

			if(count($files) > 0){
				arsort($files);
				reset($files);
				$l = key($files);
				$l = substr($l, 0, -4);
				$this->lang = isset(self::$languages[$l]) ? $l : $lang;
				$this->langfile = \pocketmine\PATH . "src/pocketmine/language/setup/" . $l . ".ini";
			}else{
				$this->lang = "tr";
				$this->langfile = \pocketmine\PATH . "src/pocketmine/language/setup/tur.ini";
			}
		}

		$this->loadLang(\pocketmine\PATH . "src/pocketmine/language/setup/tur.ini", "tur");
		if($this->lang !== "tr"){
			$this->loadLang($this->langfile, $this->lang);
		}

	}

	public function getLang(){
		return ($this->lang);
	}

	public function loadLang($langfile, $lang = "en"){
		$this->texts[$lang] = [];
		$texts = explode("\n", str_replace(["\r", "\\/\\/"], ["", "//"], file_get_contents($langfile)));
		foreach($texts as $line){
			$line = trim($line);
			if($line === ""){
				continue;
			}
			$line = explode("=", $line);
			$this->texts[$lang][trim(array_shift($line))] = trim(str_replace(["\\n", "\\N",], "\n", implode("=", $line)));
		}
	}

	public function get($name, $search = [], $replace = []){
		if(!isset($this->texts[$this->lang][$name])){
			if($this->lang !== "tr" and isset($this->texts["tr"][$name])){
				return $this->texts["tr"][$name];
			}else{
				return $name;
			}
		}elseif(count($search) > 0){
			return str_replace($search, $replace, $this->texts[$this->lang][$name]);
		}else{
			return $this->texts[$this->lang][$name];
		}
	}

	public function __get($name){
		return $this->get($name);
	}

}
