<?php

namespace Tactile\Controller;

use Tactile\Model;

trait QuantumResourceTrait
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
	
	protected function getQuantumResource($nameOverride = null)
	{
		if ($this->resource) {
			return $this->resource;
		}
		
		$name = $nameOverride ? $nameOverride : $this->params('resource_name');
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