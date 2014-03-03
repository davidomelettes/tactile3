<?php

namespace Omelettes\Controller;

use Omelettes\Form;

trait SignupFormsTrait
{
	/**
	 * @var Form\SignupForm
	 */
	protected $signupForm;
	
	/**
	 * @var Form\SignupFilter
	 */
	protected $signupFilter;
	
	public function getSignupForm()
	{
		if (!$this->signupForm) {
			$form = new Form\SignupForm();
			$this->signupForm = $form;
		}
	
		return $this->signupForm;
	}
	
	public function getSignupFilter()
	{
		if (!$this->signupFilter) {
			$userFilter = $this->getServiceLocator()->get('Omelettes\Form\SignupFilter');
			$this->signupFilter = $userFilter;
		}
	
		return $this->signupFilter;
	}
	
}
