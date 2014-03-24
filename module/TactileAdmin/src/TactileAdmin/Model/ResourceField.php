<?php

namespace TactileAdmin\Model;

use Tactile\Model\ResourceField as TactileResourceField;

class ResourceField extends TactileResourceField
{
	public function getTableHeadings()
	{
		return array(
			'label'			=> 'Field Label',
			'type'			=> 'Field Type',
		);
	}
	
	public function getTableRowPartial()
	{
		return 'tabulate/resource-field';
	}
	
}
