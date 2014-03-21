<?php

namespace TactileAdmin\Form\Resource;

class EditProtectedForm extends AbstractResourceForm
{
	public function init()
	{
		parent::init();
		
		$this->add(array(
			'name'		=> 'name',
			'type'		=> 'StaticValue',
			'options'	=> array(
				'label'			=> 'URL Slug',
			),
			'attributes'=> array(
				'id'		=> $this->getName() . 'Name',
			),
		));
	}
	
}
