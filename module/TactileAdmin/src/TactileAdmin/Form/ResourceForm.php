<?php

namespace TactileAdmin\Form;

use TactileAdmin\Model;
use Omelettes\Form\NamedItemForm;
use Zend\Form\FormInterface;

class ResourceForm extends NamedItemForm
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
	
	public function bind($resource, $flags = FormInterface::VALUES_NORMALIZED)
	{
		if ($resource->key) {
			$this->remove('name');
			$this->add(array(
				'name'		=> 'name',
				'type'		=> 'StaticValue',
				'options'	=> array(
					'label'		=> 'URL Slug',
				),
				'attributes'=> array(
					'id'		=> $this->getName() . 'Name',
				),
			));
		}
		
		parent::bind($resource, $flags);
		
		return $this;
	}
	
}
