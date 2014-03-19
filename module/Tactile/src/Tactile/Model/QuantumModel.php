<?php

namespace Tactile\Model;

use Omelettes\Model\AccountBoundNamedItemModel;

abstract class QuantumModel extends AccountBoundNamedItemModel
{
	public function getTableHeadings()
	{
		return array(
			'name'				=> 'Name',
		);
	}
	
	public function getTableRowPartial()
	{
		return 'tabulate/quantum';
	}
	
}
