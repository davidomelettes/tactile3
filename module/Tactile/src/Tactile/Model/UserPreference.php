<?php

namespace Tactile\Model;

use Omelettes\Model\AbstractModel;

class UserPreference extends AbstractModel
{
	protected $key;
	protected $name;
	protected $type = 'varchar';
	protected $value;
	protected $default;
	
	public function exchangeArray($data)
	{
		$this->key = isset($data['key']) ? $data['key'] : null;
		$this->name = isset($data['name']) ? $data['name'] : null;
		$this->type = isset($data['type']) ? $data['type'] : 'varchar';
		
		switch ($this->type) {
			case 'varchar':
			case 'integer':
			case 'numeric':
			case 'datetime':
			case 'uuid':
			case 'boolean':
				$this->default = isset($data[$this->type.'_default']) ? $data[$this->type.'_default'] : null;
				$this->value = isset($data[$this->type.'_value']) ? $data[$this->type.'_value'] : null;
				break;
			default:
				throw new \Exception('Unrecognised preference type: ' . $this->type);
		}
		
		return $this;
	}
	
	public function getArrayCopy()
	{
		$copy = array(
			'name' => $this->name,
			'type' => $this->type,
		);
		switch ($this->type) {
			case 'varchar':
			case 'integer':
			case 'numeric':
			case 'datetime':
			case 'uuid':
			case 'boolean':
				$copy[$this->type.'_value'] = $this->value;
				$copy[$this->default.'_default'] = $this->default;
				break;
			default:
				throw new \Exception('Unrecognised preference type: ' . $this->type);
		}
		
		return $copy;
	}
	
	public function setKey($key)
	{
		$this->key = $key;
		
		return $this;
	}
	
	public function getKey()
	{
		return $this->key;
	}
	
	public function setName($name)
	{
		$this->name = $name;
		
		return $this;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setType($type)
	{
		$this->type = $type;
	
		return $this;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function setValue($value)
	{
		$this->value = $value;
		
		return $this;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	public function getDefault()
	{
		return $this->default;
	}
	
}
