<?php

namespace TactileAdmin\Model;

use Tactile\Model\Resource as TactileResource;

class Resource extends TactileResource
{
	public function getTableHeadings()
	{
		return array(
			'labelPlural'	=> 'Name',
			'name'			=> 'URL Slug',
		);
	}
	
	public function getTableRowPartial()
	{
		return 'tabulate/resource';
	}
	
}
