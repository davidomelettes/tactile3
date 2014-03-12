<?php

namespace TactileAdmin\Model;

use Omelettes\Model\AccountBoundQuantumModel;

class Resource extends AccountBoundQuantumModel
{
	protected $labelSingular;
	protected $labelPlural;
	protected $nameLabel;
	
	protected $propertyMap = array(
		'labelSingular'				=> 'label_singular',
		'labelPlural'				=> 'label_plural',
		'nameLabel'					=> 'name_label',
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
