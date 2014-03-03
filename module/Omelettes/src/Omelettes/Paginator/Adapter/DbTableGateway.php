<?php

namespace Omelettes\Paginator\Adapter;

use Zend\Paginator\Adapter\DbTableGateway as ZendDbTableGatewayAdapter;

class DbTableGateway extends ZendDbTableGatewayAdapter
{
	public function getResultSetPrototype()
	{
		return $this->resultSetPrototype;
	}
	
	public function getItems($offset, $itemCountPerPage)
	{
		$resultSet = parent::getItems($offset, $itemCountPerPage);
		
		// Buffer the resultSet so we can iterate more than once
		$resultSet->buffer();
		
		return $resultSet;
	}
	
}
