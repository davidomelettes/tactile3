<?php

namespace Omelettes\Controller;

use Omelettes\Form;

trait AuthFormsTrait
{
	/**
	 * @var Form\ForgotPasswordForm
	 */
	protected $forgotPasswordForm;
	
	/**
	 * @var Form\ForgotPasswordFilter
	 */
	protected $forgotPasswordFilter;
	
	/**
	 * @var Form\LoginForm
	 */
	protected $loginForm;
	
	/**
	 * @var Form\LoginFilter
	 */
	protected $loginFilter;
	
	/**
	 * @var Form\ResetPasswordForm
	 */
	protected $resetPasswordForm;
	
	/**
	 * @var Form\ResetPasswordFilter
	 */
	protected $resetPasswordFilter;
	
	public function getLoginForm()
	{
		if (!$this->loginForm) {
			$loginForm = $this->getServiceLocator()->get('FormElementManager')->get('Omelettes\Form\LoginForm');
			$this->loginForm = $loginForm;
		}
		
		return $this->loginForm;
	}
	
	public function getLoginFilter()
	{
		if (!$this->loginFilter) {
			$loginFilter = $this->getServiceLocator()->get('Omelettes\Form\LoginFilter');
			$this->loginFilter = $loginFilter;
		}
	
		return $this->loginFilter;
	}
	
	public function getForgotPasswordForm()
	{
		if (!$this->forgotPasswordForm) {
			$this->forgotPasswordForm = new Form\ForgotPasswordForm();
		}
	
		return $this->forgotPasswordForm;
	}
	
	public function getforgotPasswordFilter()
	{
		if (!$this->forgotPasswordFilter) {
			$forgotPasswordFilter = $this->getServiceLocator()->get('Omelettes\Form\ForgotPasswordFilter');
			$this->forgotPasswordFilter = $forgotPasswordFilter;
		}
	
		return $this->forgotPasswordFilter;
	}
	
	public function getResetPasswordForm()
	{
		if (!$this->resetPasswordForm) {
			$this->resetPasswordForm = new Form\ResetPasswordForm();
		}
	
		return $this->resetPasswordForm;
	}
	
	public function getResetPasswordFilter()
	{
		if (!$this->resetPasswordFilter) {
			$resetPasswordFilter = new Form\ResetPasswordFilter();
			$this->resetPasswordFilter = $resetPasswordFilter;
		}
	
		return $this->resetPasswordFilter;
	}
	
}
