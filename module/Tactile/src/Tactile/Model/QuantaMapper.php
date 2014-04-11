<?php

namespace Tactile\Model;

use Omelettes\Model\AccountBoundNamedItemsMapper;
use Zend\Db\Sql\Predicate,
	Zend\Db\Sql\Select;

class QuantaMapper extends AccountBoundNamedItemsMapper
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
	
	protected function prepareSaveData(Quantum $model)
	{
		$data = parent::prepareSaveData($model);
		$data = array_merge(
			$data,
			array(
				'resource_name'		=> $model->resource->name,
				'xml_specification'	=> $model->toXml(),
			)
		);
	
		return $data;
	}
	
	public function saveModel(Quantum $model)
	{
		parent::saveModel($model);

		// Save searchable field values
	}
	
}
