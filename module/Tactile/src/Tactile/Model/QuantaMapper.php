<?php

namespace Tactile\Model;

use Omelettes\Model\AccountBoundNamedItemsMapper;
use Zend\Db\Sql\Predicate;

abstract class QuantaMapper extends AccountBoundNamedItemsMapper
{
	/**
	 * @var Resource
	 */
	protected $resource;
	
	public function setResource(Resource $resource)
	{
		if ($this->resource) {
			throw new \Exception('Resource already set');
		}
		$this->resource = $resource;
		
		return $this;
	}
	
	protected function getDefaultWhere()
	{
		if (!$this->resource) {
			throw new \Exception('Resource not set');
		}
		
		$where = parent::getDefaultWhere();
		$where->addPredicate(new Predicate\IsNull('current_version_key'));
		$where->addPredicate(new Predicate\Operator('resource_name', '=', $this->resource->name));
		
		return $where;
	}
	
	protected function prepareSaveData(QuantumModel $model)
	{
		$data = parent::prepareSaveData($model);
		$data['resource_name'] = $this->resource->name;
	
		return $data;
	}
	
}
