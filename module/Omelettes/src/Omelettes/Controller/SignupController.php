<?php

namespace Omelettes\Controller;

use Omelettes\Form;
use Omelettes\Model;
use Zend\Form\FormInterface;

class SignupController extends AbstractController
{
	use AuthUsersTrait;
	use SignupFormsTrait;
	
	public function signupAction()
	{
		if ($this->getAuthService()->hasIdentity()) {
			$this->flashMessenger()->addErrorMessage('You are already logged in');
			return $this->redirect('front');
		}
		
		$form = $this->getSignupForm();
		$request = $this->getRequest();
		$user = new Model\AuthUser();
		$form->bind($user);
		
		if ($request->isPost()) {
			$form->setInputFilter($this->getSignupFilter()->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid()) {
				$formData = $form->getData(FormInterface::VALUES_AS_ARRAY);
				$this->getUsersMapper()->beginTransaction();
				try {
					// Create account
					$this->getUsersMapper()->signupUser($user, $formData['password']);
					
					// Log in
					$user->setPasswordAuthenticated();
					$this->getAuthService()->getStorage()->write($user);
					
				} catch (Exception $e) {
					$this->getAuthService()->clearIdentity();
					$this->getUsersMapper()->rollbackTransaction();
					$this->flashMessenger('A problem occurred during the sign up process, please try again');
					return $this->redirect('signup');
				}
				$this->getUsersMapper()->commitTransaction();
				
				return $this->redirect()->toRoute('home');
			}
		}
		
		return array(
			'form' => $form,
		);
	}
	
}