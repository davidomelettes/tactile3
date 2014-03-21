<?php

namespace TactileAdmin\Form\Resource;

use Omelettes\Form\NamedItemForm;

abstract class AbstractResourceForm extends NamedItemForm
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
				'feedback'	=> 'glyphicon glyphicon-arrow-down',
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
	
		$this->addSubmitFieldset();
	}
	
}
