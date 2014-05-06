<?php

namespace Omelettes\Form;

class NamedItemForm extends AbstractForm
{
	public function addKeyElement()
	{
		$this->add(array(
			'name'		=> 'key',
			'type'		=> 'Hidden',
		));
	
		return $this;
	}
	
	public function addNameElement($label = 'Name')
	{
		$this->add(array(
			'name'		=> 'name',
			'type'		=> 'Text',
			'options'	=> array(
				'label'		=> $label,
			),
			'attributes'=> array(
				'id'		=> $this->getName() . 'Name',
			),
		));
	
		return $this;
	}
	
	public function init()
	{
		$this->addNameElement('Name');
	
		$this->addSubmitFieldset('Save', 'btn btn-success', 'Saving...');
	}
	
}
