<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\plugin;

use LogLevel;
use pocketmine\Server;

class PluginLogger implements \AttachableLogger{

	private $pluginName;
	
	private $attachments = [];

	public function addAttachment(\LoggerAttachment $attachment){
		$this->attachments[spl_object_hash($attachment)] = $attachment;
	}

	public function removeAttachment(\LoggerAttachment $attachment){
		unset($this->attachments[spl_object_hash($attachment)]);
	}

	public function removeAttachments(){
		$this->attachments = [];
	}

	public function getAttachments(){
		return $this->attachments;
	}

	/**
	 * @param Plugin $context
	 */
	public function __construct(Plugin $context){
		$prefix = $context->getDescription()->getPrefix();
		$this->pluginName = $prefix != null ? "[$prefix] " : "[" . $context->getDescription()->getName() . "] ";
	}

	public function emergency($message){
		$this->log(LogLevel::EMERGENCY, $message);
	}

	public function alert($message){
		$this->log(LogLevel::ALERT, $message);
	}

	public function critical($message){
		$this->log(LogLevel::CRITICAL, $message);
	}

	public function error($message){
		$this->log(LogLevel::ERROR, $message);
	}

	public function warning($message){
		$this->log(LogLevel::WARNING, $message);
	}

	public function notice($message){
		$this->log(LogLevel::NOTICE, $message);
	}

	public function info($message){
		$this->log(LogLevel::INFO, $message);
	}

	public function debug($message){
		$this->log(LogLevel::DEBUG, $message);
	}

	public function logException(\Throwable $e, $trace = null){
		Server::getInstance()->getLogger()->logException($e, $trace);
	}

	public function log($level, $message){
		Server::getInstance()->getLogger()->log($level, $this->pluginName . $message);
		foreach($this->attachments as $attachment){
			$attachment->log($level, $message);
		}
	}
}
