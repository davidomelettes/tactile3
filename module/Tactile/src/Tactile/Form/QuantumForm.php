<?php

namespace Tactile\Form;

use Tactile\Model;
use Omelettes\Form\NamedItemForm;

class QuantumForm extends NamedItemForm
{
	/**
	 * @var Model\Resource
	 */
	protected $resource;
	
	public function setResource(Model\Resource $resource)
	{
		$this->resource = $resource;
		
		$fieldsMapper = $this->getApplicationServiceLocator()->get('Tactile\Model\ResourceFieldsMapper');
		$fields = $fieldsMapper->fetchForResource($this->resource);
		foreach ($fields as $field) {
			$this->add($field->getInputElementSpecification());
		}
		
		$this->get('submit')->get('submit')->setValue('Save ' . $resource->labelSingular);
		
		return $this;
	}
	
}
