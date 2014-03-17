<?php

namespace Tactile\Controller;

use Tactile\Model;

trait ResourceTrait
{
	/**
	 * @var Model\ResourcesMapper
	 */
	protected $resourcesMapper;
	
	/**
	 * @var Model\Resource
	 */
	protected $resource;
	
	/**
	 * @return Model\ResourcesMapper
	 */
	public function getResourcesMapper()
	{
		if (!$this->resourcesMapper) {
			$resourcesMapper = $this->getServiceLocator()->get('Tactile\Model\ResourcesMapper');
			$this->resourcesMapper = $resourcesMapper;
		}
	
		return $this->resourcesMapper;
	}
	
	/**
	 * @return Model\Resource
	 */
	public function getResource()
	{
		if (!$this->resource) {
			$model = new Model\Resource();
			$this->resource = $model;
		}
	
		return $this->resource;
	}
	
	protected function findRequestedResource()
	{
		$name = $this->params('resource_name');
		if ($name) {
			$model = $this->getResourcesMapper()->findByName($name);
			if (!$model) {
				// Invalid route, should handle as 404
				return false;
			}
			$this->resource = $model;
			return $this->resource;
		}
	
		return false;
	}
	
}