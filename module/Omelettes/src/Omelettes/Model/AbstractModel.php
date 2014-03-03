<?php

namespace Omelettes\Model;

abstract class AbstractModel
{
	public function __construct($data = array())
	{
		$this->exchangeArray($data);
	}
	
	public function __get($name)
	{
		$method = 'get' . $name;
		if (!method_exists($this, $method)) {
			throw new \Exception('Invalid model property: ' . $name);
		}
		return $this->$method();
	}
	
	public function __set($name, $value)
	{
		$method = 'set' . $name;
		if ('mapper' == $name || !method_exists($this, $method)) {
			throw new \Exception('Invalid model property: ' . $name);
		}
		$this->$method($value);
	}
	
	public function setOptions($options)
	{
		foreach ($options as $key => $value) {
			$this->__set($key, $value);
		}
		
		return $this;
	}
	
	abstract public function exchangeArray($data);
	
	abstract public function getArrayCopy();
	
}
