<?php

namespace TactileAdmin\Form\Resource;

class AddForm extends AbstractResourceForm
{
	public function init()
	{
		parent::init();
		
		$config = $this->getApplicationServiceLocator()->get('config');
		$basePath = preg_replace('/^https?:\/\//', '', $config['view_manager']['base_path']);
		$this->add(array(
			'name'		=> 'name',
			'type'		=> 'Text',
			'options'	=> array(
				'label'			=> 'URL Slug',
				'prefix'		=> $basePath.'/',
				'help_text'		=> '* Cannot be changed once set',
			),
			'attributes'=> array(
				'id'		=> $this->getName() . 'Name',
			),
		));
	}
	
}
