<?php

namespace Omelettes\Model;

use Omelettes\Model\AbstractModel;
use OmelettesAuth\Model\User;

class QuantumModel extends AbstractModel implements Tabulatable, \JsonSerializable
{
	/**
	 * Array of model properties => database columns
	 * 
	 * @var array
	 */
	protected $propertyMap = array();

	/**
	 * Property map for base quantum properties
	 *
	 * @var array
	 */
	protected $quantumPropertyMap = array(
		'key'				=> 'key',
		'name'				=> 'name',
		'created'			=> 'created',
		'updated'			=> 'updated',
		'createdBy'			=> 'created_by',
		'updatedBy'			=> 'updated_by',
	);

	protected $key;
	protected $name;
	protected $created;
	protected $updated;
	protected $createdBy;
	protected $updatedBy;

	public function __construct($data = array())
	{
		if (!empty($data)) {
			$this->exchangeArray($data);
		}
	}

	public function __get($name)
	{
		$map = $this->getPropertyMap();
		$getterMethodName = 'get' . ucfirst($name);
		if (!method_exists($this, $getterMethodName) && !isset($map[$name])) {
			throw new \Exception('Invalid ' . get_class($this) . ' property: ' . $name);
		}

		return $this->$getterMethodName();
	}

	public function __set($name, $value)
	{
		$map = $this->getPropertyMap();
		$setterMethodName = 'set' . ucfirst($name);
		if (!method_exists($this, $setterMethodName) && !isset($map[$name])) {
			throw new \Exception('Invalid ' . get_class($this) . ' property: ' . $name);
		}

		return $this->$setterMethodName($value);
	}

	public function __call($function, array $args)
	{
		if (preg_match('/(get|set)(.+)/', $function, $m)) {
			$map = $this->getPropertyMap();
			$property = lcfirst($m[2]);
			if (isset($map[$property])) {
				if ('get' === $m[1]) {
					// Getting a model property
					return $this->$property;
				} else {
					// Setting a model property
					$this->$property = $args[0];
						
					return $this;
				}
			}
		}
	}
	
	protected function getPropertyMap()
	{
		return array_merge($this->quantumPropertyMap, $this->propertyMap);
	}
	

	public function jsonSerialize()
	{
		return $this->getArrayCopy();
	}

	public function exchangeArray($data)
	{
		$map = $this->getPropertyMap();
		foreach ($map as $property => $column) {
			$setterMethodName = 'set'.ucfirst($property);
			$this->$setterMethodName(isset($data[$column]) ? $data[$column] : null);
		}

		return $this;
	}

	public function getArrayCopy()
	{
		$copy = array();
		$map = $this->getPropertyMap();
		foreach ($map as $property => $column) {
			$getterMethodName = 'get'.ucfirst($property);
			$copy[$column] = $this->$getterMethodName();
		}

		return $copy;
	}

	public function setKey($key)
	{
		$this->key = (string)$key;

		return $this;
	}

	public function getKey()
	{
		$key = $this->key;
		if ($key) {
			$key = str_replace('-', '', $key);
		}
		
		return $key;
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
	
	public function setCreated($ts)
	{
		$this->created = $ts;

		return $this;
	}

	public function getCreated()
	{
		return $this->created;
	}

	public function setUpdated($ts)
	{
		$this->updated = $ts;

		return $this;
	}

	public function getUpdated()
	{
		return $this->updated;
	}

	public function setCreatedBy($key)
	{
		$this->createdBy = $key;

		return $this;
	}

	public function getCreatedBy()
	{
		return $this->createdBy;
	}

	public function setUpdatedBy($key)
	{
		$this->updatedBy = $key;

		return $this;
	}

	public function getUpdatedBy()
	{
		return $this->updatedBy;
	}

	public function getCreatedByFullName()
	{
		return $this->createdByFullName;
	}

	public function getUpdatedByFullName()
	{
		return $this->updatedByFullName;
	}

	public function getTableHeadings()
	{
		return array(
			'name'	=> 'Name',
		);
	}

	public function getTableRowPartial()
	{
		return 'tabulate/quantum';
	}

}
