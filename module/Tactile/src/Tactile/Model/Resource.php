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
	
	protected $fields = null;
	
	protected $propertyMap = array(
		'labelSingular'				=> 'label_singular',
		'labelPlural'				=> 'label_plural',
		'nameLabel'					=> 'name_label',
		'protected'					=> 'protected',
	);
	
	public function getFields()
	{
		if (is_null($this->fields)) {
			$fieldsMapper = $this->getServiceLocator()->get('Tactil\Model\ResourceFieldsMapper');
			$fields = $fieldsMapper->fetchForResource($this);
			$this->fields = $fields;
		}
		
		return $this->fields;
	}
	
}
