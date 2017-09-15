<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\entity;

class Skin{

	const SINGLE_SKIN_SIZE = 64 * 32 * 4;
	const DOUBLE_SKIN_SIZE = 64 * 64 * 4;

	const MODEL_STEVE = "Standard_Steve";
	const MODEL_ALEX = "Standard_Alex";
	
	protected $data;
	protected $model;

	public function __construct($data, $model){
		$this->data = $data;
		$this->model = $model;
	}

	public function getData(){
		return $this->data;
	}

	public function getModel(){
		return $this->model;
	}

	public function setData($data){
		if(strlen($data) != self::SINGLE_SKIN_SIZE && strlen($data) != self::DOUBLE_SKIN_SIZE){
			Server::getInstance()->getLogger()->critical("Geçersiz Skin!");
			return false;
		}
		$this->data = $data;
	}

	public function setModel($model){
		if($model == "") $model = self::MODEL_STEVE;
		$this->model = $model;
	}

}