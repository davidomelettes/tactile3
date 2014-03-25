<?php

namespace Tactile\Model;

use Omelettes\Model\AccountBoundNamedItemModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait;

class Resource extends AccountBoundNamedItemModel implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	
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
