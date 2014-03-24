<?php

namespace Tactile\Model;

use Omelettes\Model\AccountBoundNamedItemModel;

class Resource extends AccountBoundNamedItemModel
{
	protected $labelSingular;
	protected $labelPlural;
	protected $nameLabel;
	protected $protected;
	
	protected $propertyMap = array(
		'labelSingular'				=> 'label_singular',
		'labelPlural'				=> 'label_plural',
		'nameLabel'					=> 'name_label',
		'protected'					=> 'protected',
	);
	
}
