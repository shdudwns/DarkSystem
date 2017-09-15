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

use pocketmine\command\CommandExecutor;

interface Plugin extends CommandExecutor{
	
	public function onLoad();
	
	public function onEnable();

	public function isEnabled();
	
	public function onDisable();

	public function isDisabled();
	
	public function getDataFolder();
	
	public function getDescription();
	
	public function getResource($filename);
	
	public function saveResource($filename, $replace = false);
	
	public function getResources();
	
	public function getConfig();

	public function saveConfig();

	public function saveDefaultConfig();

	public function reloadConfig();
	
	public function getServer();

	public function getName();
	
	public function getLogger();
	
	public function getPluginLoader();

}