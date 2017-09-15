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

class AttributeMap implements \ArrayAccess{
	
	private $attributes = [];

	public function addAttribute(Attribute $attribute){
		$this->attributes[$attribute->getId()] = $attribute;
	}
	
	public function getAttribute(int $id){
		return $this->attributes[$id] ?? null;
	}

	public function getAll(){
		return $this->attributes;
	}
	
	public function needSend(){
		return array_filter($this->attributes, function (Attribute $attribute){
			return $attribute->isSyncable() and $attribute->isDesynchronized();
		});
	}

	public function offsetExists($offset){
		return isset($this->attributes[$offset]);
	}

	public function offsetGet($offset){
		return $this->attributes[$offset]->getValue();
	}

	public function offsetSet($offset, $value){
		$this->attributes[$offset]->setValue($value);
	}

	public function offsetUnset($offset){
		throw new \RuntimeException("Could not unset an attribute from an attribute map");
	}
}
