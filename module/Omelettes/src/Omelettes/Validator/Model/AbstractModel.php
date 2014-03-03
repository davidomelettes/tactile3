<?php

namespace Omelettes\Validator\Model;

use Omelettes\Model\AbstractMapper;
use Zend\Validator\AbstractValidator,
	Zend\Validator\Exception;

abstract class AbstractModel extends AbstractValidator
{
	/**
	 * @var QuantumMapper
	 */
	protected $mapper;
	
	/**
	 * @var string
	 */
	protected $mapperMethod = 'find';
	
	/**
	 * @var array
	 */
	protected $mapperMethodParameters = array();
	
	public function __construct($options = null)
	{
		if ($options instanceof AbstractMapper) {
			$this->setMapper($options);
			return;
		}
		
		if (!array_key_exists('mapper', $options)) {
			throw new Exception\InvalidArgumentException('Mapper option missing!');
		}
		$this->setMapper($options['mapper']);
		
		if (array_key_exists('method', $options)) {
			$this->setMapperMethod($options['method']);
		}
		
		parent::__construct($options);
	}
	
	public function setMapper(AbstractMapper $mapper)
	{
		$this->mapper = $mapper;
		
		return $this;
	}
	
	public function setMapperMethod($methodName)
	{
		if (!$this->mapper) {
			throw new Exception\RuntimeException('Mapper not set');
		}
		if (!method_exists($this->mapper, $methodName)) {
			throw new Exception\InvalidArgumentException('Invalid mapper method: ' . $methodName);
		}
		$this->mapperMethod = $methodName;
		
		return $this;
	}
	
}
