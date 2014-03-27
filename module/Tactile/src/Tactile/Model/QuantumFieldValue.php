<?php

namespace Tactile\Model;

class QuantumFieldValue
{
	protected $type;
	protected $value;
	
	public function __construct($type, $value = null)
	{
		$this->setType($type);
		$this->setValue($value);
	}
	
	public function setType($type)
	{
		if (!in_array($type, array('varchar', 'text', 'datetime', 'integer', 'numeric', 'user', 'option'))) {
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