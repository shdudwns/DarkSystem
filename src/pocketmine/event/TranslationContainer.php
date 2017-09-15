<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\event;

class TranslationContainer extends TextContainer{
	
	protected $params = [];

	/**
	 * @param string   $text
	 * @param string[] $params
	 */
	public function __construct($text, array $params = []){
		parent::__construct($text);
		$this->setParameters($params);
	}

	/**
	 * @return string[]
	 */
	public function getParameters(){
		return $this->params;
	}

	/**
	 * @param int $i
	 *
	 * @return string
	 */
	public function getParameter($i){
		return isset($this->params[$i]) ? $this->params[$i] : null;
	}

	/**
	 * @param int    $i
	 * @param string $str
	 */
	public function setParameter($i, $str){
		if($i < 0 or $i > count($this->params)){
			throw new \InvalidArgumentException("Invalid index $i, have " . count($this->params));
		}

		$this->params[(int) $i] = $str;
	}

	/**
	 * @param string[] $params
	 */
	public function setParameters(array $params){
		$i = 0;
		foreach($params as $str){
			$this->params[$i] = (string) $str;
			++$i;
		}
	}
}