<?php

namespace TactileAdmin\Model;

use Tactile\Model\Resource as TactileResource;

class Resource extends TactileResource
{
	protected $protected;
	
	protected $propertyMap = array(
		'labelSingular'				=> 'label_singular',
		'labelPlural'				=> 'label_plural',
		'nameLabel'					=> 'name_label',
		'protected'					=> 'protected',
	);
	
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
