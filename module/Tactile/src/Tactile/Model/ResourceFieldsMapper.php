<?php

namespace Tactile\Model;

use Omelettes\Model\AccountBoundNamedItemsMapper;
use Zend\Db\Sql\Predicate;

class ResourceFieldsMapper extends AccountBoundNamedItemsMapper
{
	protected function getDefaultOrder()
	{
		return array('priority DESC', 'name');
	}
	
	public function fetchForResource(Resource $resource, $paginated = false)
	{
		$where = $this->getWhere();
		$where->addPredicate(new Predicate\Operator('resource_name', '=', $resource->name));
		
		return $this->fetchAllWhere($where, $paginated);
	}
	
}
