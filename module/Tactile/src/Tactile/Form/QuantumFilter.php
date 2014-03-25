<?php

namespace Tactile\Form;

use Omelettes\Form\NamedItemFilter;

class QuantumFilter extends NamedItemFilter
{
	/**
	 * @var Model\Resource
	 */
	protected $resource;
	
	public function setResource(Model\Resource $resource)
	{
		$this->resource = $resource;
	
		return $this;
	}
	
	public function getInputFilter()
	{
		if (!$this->resource) {
			throw new \Exception('Resource not set');
		}
		
		if (!$this->inputFilter) {
			$inputFilter = parent::getInputFilter();
			$factory = $inputFilter->getFactory();
			
			$fieldsMapper = $this->getApplicationServiceLocator()->get('Tactile\Model\ResourceFieldsMapper');
			$fields = $fieldsMapper->fetchForResource($this->resource);
			foreach ($fields as $field) {
				$this->add($factory->createInput($field->getInputFilterSpecification()));
			}
			
			$this->inputFilter = $inputFilter;
		}
		
		return $this->inputFilter;
	}
	
}
