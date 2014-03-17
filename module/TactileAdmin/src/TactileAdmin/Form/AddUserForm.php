<?php

namespace TactileAdmin\Form;

use Omelettes\Form\NamedItemForm;

class AddUserForm extends NamedItemForm
{
	public function __construct($name = 'form-add-user')
	{
		parent::__construct($name);
	}
	
	public function init()
	{
		$this->addNameElement('Email Address');
	
		$this->add(array(
			'name'		=> 'full_name',
			'type'		=> 'Text',
			'options'	=> array(
				'label'		=> 'Full Name',
			),
			'attributes'=> array(
				'id'			=> $this->getName() . 'FullName',
				'autocomplete'	=> 'off',
			),
		));
		
		$this->add(array(
			'name'		=> 'password',
			'type'		=> 'Text',
			'options'	=> array(
				'label'		=> 'Password',
				'help_text'	=> 'Leave blank to have a randomly generated password sent to the user',
			),
			'attributes'=> array(
				'id'			=> $this->getName() . 'Password',
				'autocomplete'	=> 'off',
			),
		));
		
		$this->add(array(
			'name'		=> 'acl_role',
			'type'		=> 'Select',
			'options'	=> array(
				'label'			=> 'User Role',
				'help_text'	=> 'Admin Users can make changes to your Account',
				'value_options' => array(
					'user'	=> 'Normal User',
					'admin'	=> 'Admin User',
				),
			),
			'attributes'=> array(
				'id'			=> $this->getName() . 'Role',
			),
		));
		
		$this->addSubmitFieldset();
	}
	
}
