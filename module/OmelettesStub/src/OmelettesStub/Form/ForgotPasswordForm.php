<?php

namespace OmelettesAuth\Form;

use Omelettes\Form\NamedItemForm;

class ForgotPasswordForm extends NamedItemForm
{
	public function __construct($name = 'form-forgot-password')
	{
		parent::__construct($name);
		
		$this->addNameElement('Email Address');
		$this->get('name')->setAttribute('placeholder', 'Email Address');
		
		$this->addSubmitFieldset('Reset password', 'btn btn-lg btn-block btn-primary');
	}
	
	public function init()
	{
		
	}
	
}