<?php

namespace OmelettesStub\Form;

use Omelettes\Form\QuantumForm;

class LoginForm extends QuantumForm
{
	public function __construct($name = 'form-login')
	{
		parent::__construct($name);
	}
	
	public function init()
	{
		$this->addNameElement('Email Address');
		$this->get('name')->setAttribute('placeholder', 'Email Address');
		
		$this->add(array(
			'name'		=> 'password',
			'type'		=> 'Password',
			'options'	=> array(
				'label'		=> 'Password',
			),
			'attributes'=> array(
				'id'			=> $this->getName() . 'Password',
				'placeholder'	=> 'Password',
			),
		));
		
		$router = $this->getApplicationServiceLocator()->get('Router');
		$url = $router->assemble(array(), array('name' => 'forgot-password'));
		$this->add(array(
			'name'		=> 'forgot',
			'type'		=> 'StaticValue',
			'options'	=> array(
				'label'			=> 'Password',
				'escape_html'	=> false,
			),
			'attributes'=> array(
				'id'			=> $this->getName() . 'Forgot',
				'value'			=> sprintf('<a href="%s">%s</a>', $url, 'Forgot Password?'),
			),
		));
		
		$this->add(array(
			'name'		=> 'remember_me',
			'type'		=> 'Checkbox',
			'options'	=> array(
				'label'		=> 'Keep me signed in?',
			),
			'attributes'=> array(
				'id'		=> $this->getName() . 'RememberMe',
			),
		));
		
		$this->addSubmitFieldset('Sign in', 'btn btn-lg btn-block btn-primary');
	}
	
}