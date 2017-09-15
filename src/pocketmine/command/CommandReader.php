<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\command;

use pocketmine\Thread;

class CommandReader extends Thread{
	private $readline;
	protected $buffer;
	private $shutdown = false;

	public function __construct(){
		$this->buffer = new \Threaded;
		$this->start();
	}

	public function shutdown(){
		$this->shutdown = true;
	}

	private function readLine(){
		if(!$this->readline){
			global $stdin;
			if(!is_resource($stdin)){
				return "";
			}
			return trim(fgets($stdin));
		}else{
			$line = trim(readline("> "));
			if($line != ""){
				readline_add_history($line);
			}
			return $line;
		}
	}
	
	public function getLine(){
		if($this->buffer->count() !== 0){
			return $this->buffer->shift();
		}
		return null;
	}

	public function run(){
		$opts = getopt("", ["disable-readline"]);
		if(extension_loaded("readline") and !isset($opts["disable-readline"])){
			$this->readline = true;
		}else{
			global $stdin;
			$stdin = fopen("php://stdin", "r");
			stream_set_blocking($stdin, 0);
			$this->readline = false;
		}
		$lastLine = microtime(true);
		while(!$this->shutdown){
			if(($line = $this->readLine()) !== ""){
				$this->buffer[] = preg_replace("#\\x1b\\x5b([^\\x1b]*\\x7e|[\\x40-\\x50])#", "", $line);
			}elseif(!$this->shutdown and (microtime(true) - $lastLine) <= 0.1){
				$this->synchronized(function(){
					$this->wait(10000);
				});
			}
			$lastLine = microtime(true);
		}
	}

	public function getThreadName(){
		return "SUNUCU";
	}
}
