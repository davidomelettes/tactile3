<?php

namespace TactileAdmin\Form;

use Omelettes\Form\NamedItemForm;

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
		
		$config = $this->getApplicationServiceLocator()->get('config');
		$basePath = preg_replace('/^https?:\/\//', '', $config['view_manager']['base_path']);
		$this->add(array(
			'name'		=> 'name',
			'type'		=> 'Text',
			'options'	=> array(
				'label'		=> 'URL Slug',
				'prefix'	=> $basePath.'/',
			),
			'attributes'=> array(
				'id'		=> $this->getName() . 'Name',
			),
		));
		
		$this->addSubmitFieldset();
	}
	
}
