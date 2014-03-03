<?php

namespace Omelettes\Form\Element;

use Zend\Form\Element;

class Url extends Element
{
	protected $route;
	
	protected $routeOptions = array();
	
	protected $anchorClass = '';
	
	protected $attributes = array(
		'type' => 'url',
	);
	
	public function setOptions($options)
	{
		parent::setOptions($options);
		
		if (!isset($options['route'])) {
			throw new \Exception('Expected a route');
		}
		$this->setRoute($options['route']);
		
		if (isset($options['route_options']) && is_array($options['route_options'])) {
			$this->setRouteOptions($options['route_options']);
		}
		
		if (isset($options['anchor_class'])) {
			$this->setAnchorClass($options['anchor_class']);
		}
		
		return $this;
	}
	
	public function setRoute($route)
	{
		$this->route = $route;
		
		return $this;
	}
	
	public function getRoute()
	{
		return $this->route;
	}
	
	public function setRouteOptions($options)
	{
		$this->routeOptions = $options;
	
		return $this;
	}
	
	public function getRouteOptions()
	{
		return $this->routeOptions;
	}
	
	public function setAnchorClass($class)
	{
		$this->anchorClass = $class;
		
		return $this;
	}
	
	public function getAnchorClass()
	{
		return $this->anchorClass;
	}
	
}
