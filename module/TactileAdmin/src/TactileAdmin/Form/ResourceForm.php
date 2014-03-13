<?php

namespace TactileAdmin\Form;

use Omelettes\Form\QuantumForm;

class ResourceForm extends QuantumForm
{
	public function __construct($name = 'form-resource')
	{
		parent::__construct($name);
	}
	
	public function init()
	{
		$this->add(array(
			'name'		=> 'label_singular',
			'type'		=> 'Text',
			'options'	=> array(
				'label'		=> 'Singular Name',
			),
			'attributes'=> array(
				'id'			=> $this->getName() . 'SingularName',
				'autocomplete'	=> 'off',
			),
		));
		
		$this->add(array(
			'name'		=> 'label_plural',
			'type'		=> 'Text',
			'options'	=> array(
				'label'		=> 'Plural Name',
			),
			'attributes'=> array(
				'id'			=> $this->getName() . 'PluralName',
				'autocomplete'	=> 'off',
			),
		));
		
		$this->add(array(
			'name'		=> 'name',
			'type'		=> 'Text',
			'options'	=> array(
				'label'		=> 'URL Slug',
				'prefix'	=> '/',
			),
			'attributes'=> array(
				'id'		=> $this->getName() . 'Name',
			),
		));
		
		$this->addSubmitFieldset();
	}
	
}
