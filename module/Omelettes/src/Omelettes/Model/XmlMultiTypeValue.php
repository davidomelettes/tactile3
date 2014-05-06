<?php

namespace Omelettes\Model;

class XmlMultiTypeValue
{
	protected $validTypes = array('varchar', 'text', 'datetime', 'integer', 'numeric');
	protected $type;
	protected $value;
	
	public function __construct($type, $value = null)
	{
		$this->setType($type);
		$this->setValue($value);
	}
	
	public function setType($type)
	{
		if (!in_array($type, $this->validTypes)) {
			throw new \Exception('Unexpected type: ' . $type);
		}
		$this->type = $type;
		
		return $this;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function setValue($value)
	{
		switch ($this->type) {
			default:
				$this->value = $value;
		}
		
		return $this;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
}
