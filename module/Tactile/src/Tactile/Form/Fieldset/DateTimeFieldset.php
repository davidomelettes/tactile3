<?php

namespace Tactile\Form\Fieldset;

use Tactile\Model;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class DateTimeFieldset extends Fieldset
{
	/**
	 * @var Model\ResourceField
	 */
	protected $field;
	
	public function __construct(Model\ResourceField $field)
	{
		parent::__construct($field->name);
		$this->field = $field;
		
		$this->add(array(
			'name'		=> 'date',
			'required'	=> $field->required,
			'type'		=> 'Text',
			'options'	=> array(
				'label'		=> $field->label,
			),
			'attributes'=> array(
				'id'			=> $this->getName() .'-date',
				'class'		=> 'form-control datepick',
			),
		));
		$this->add(array(
			'name'		=> 'time',
			'type'		=> 'Text',
			'options'	=> array(
			),
			'attributes'=> array(
				'id'			=> $this->getName() .'-time',
				'class'		=> 'form-control timepick',
			),
		));
	}
	
}
