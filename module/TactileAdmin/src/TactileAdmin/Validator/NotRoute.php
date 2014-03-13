<?php

namespace TactileAdmin\Validator;

use Zend\Validator\AbstractValidator;

class NotRoute extends AbstractValidator
{
	const IS_ROUTE = 'isRoute';
	
	/**
	 * @var array
	 */
	protected $routeList;
	
	public function __construct($options = null)
	{
		parent::__construct($options);
	
		if (!array_key_exists('routeList', $options)) {
			throw new Exception\InvalidArgumentException('routeList option missing!');
		}
		$this->setRouteList($options['routeList']);
	}
	
	public function setRouteList(array $routeList)
	{
		$this->routeList = $routeList;
		
		return $this;
	}
	
	protected $messageTemplates = array(
		self::IS_ROUTE => "The route '%value%' is a reserved word",
	);
	
	public function isValid($value)
	{
		$this->setValue($value);
		
		if (in_array($value, $this->routeList)) {
			$this->error(self::IS_ROUTE);
			return false;
		}
		
		return true;
	}
	
}
