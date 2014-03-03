<?php

namespace OmelettesAuth\Form;

use Omelettes\Form\QuantumForm;

class ResetPasswordForm extends QuantumForm
{
	public function __construct($name = 'form-reset-password')
	{
		parent::__construct($name);
		
		$this->add(array(
			'name'		=> 'password_new',
			'type'		=> 'Password',
			'options'	=> array(
				'label'		=> 'New Password',
			),
			'attributes'=> array(
				'id'			=> $this->getName() . 'NewPassword',
				'placeholder'	=> 'New Password',
				'autocomplete'	=> 'off',
			),
		));
		$this->add(array(
			'name'		=> 'password_verify',
			'type'		=> 'Password',
			'options'	=> array(
				'label'		=> 'Verify Password',
			),
			'attributes'=> array(
				'id'			=> $this->getName() . 'VerifyPassword',
				'placeholder'	=> 'Verify Password',
				'autocomplete'	=> 'off',
			),
		));
		
		$this->addSubmitFieldset('Change Password', 'btn btn-primary btn-lg btn-block');
	}
	
}