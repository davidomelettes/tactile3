<?php

namespace TactileAdmin\Form\Resource;

class EditUnprotectedForm extends AbstractResourceForm
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
			),
			'attributes'=> array(
				'id'		=> $this->getName() . 'Name',
			),
		));
	}
	
}
