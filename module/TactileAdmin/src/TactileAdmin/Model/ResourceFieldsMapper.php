<?php

namespace TactileAdmin\Model;

use Tactile\Model\ResourceFieldsMapper as TactileResourceFieldsMapper;
use Zend\Db\Sql\Predicate;

class ResourceFieldsMapper extends TactileResourceFieldsMapper
{
	protected function prepareSaveData(Resource $model)
	{
		$data = parent::prepareSaveData($model);
		
		$data = array_merge($data, array(
		));
		
		return $data;
	}
	
}
