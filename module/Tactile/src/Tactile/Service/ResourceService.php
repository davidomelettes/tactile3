<?php

namespace Tactile\Service;

use Tactile\Model;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait;

class ResourceService implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	
	/**
	 * @var Model\ResourcesMapper
	 */
	protected $resourcesMapper;
	
	protected $resources = array();
	
	public function __construct(Model\ResourcesMapper $mapper)
	{
		$this->resourcesMapper = $mapper;
		
		$this->loadResources();
	}
	
	public function loadResources()
	{
		$this->resources = array();
		$resources = $this->resourcesMapper->fetchAll();
		foreach ($resources as $resource) {
			$this->resources[$resource->name] = $resource;
		}
		
		return $this;
	}
	
	public function getResource($name)
	{
		if (!isset($this->resources[$name])) {
			return false;
		}
		
		return $this->resources[$name];
	}
	
}