<?php

namespace Omelettes\Controller;

use Omelettes\Form;
use Omelettes\Model;
use Zend\Form\FormInterface;

class SignupController extends AbstractController
{
	use AccountsTrait;
	use AuthUsersTrait;
	use SignupFormsTrait;
	
	protected function postDispatch()
	{
		$this->addPageTitleSegment('Sign Up');
		
		return;
	}
	
	protected function postSignupSetup()
	{
		return true;
	}
	
	public function signupAction()
	{
		if ($this->getAuthService()->hasIdentity()) {
			$this->flashMessenger()->addErrorMessage('You are already logged in. Please sign out if you wish to create another account.');
			return $this->redirect()->toRoute('home');
		}
		
		$form = $this->getSignupForm();
		$request = $this->getRequest();
		$account = new Model\Account();
		$user = new Model\AuthUser();
		$form->bind($user);
		
		if ($request->isPost()) {
			$form->setInputFilter($this->getSignupFilter()->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid()) {
				$formData = $form->getData(FormInterface::VALUES_AS_ARRAY);
				
				$this->getUsersMapper()->beginTransaction();
				try {
					// Create user
					$user->aclRole = 'admin';
					$this->getUsersMapper()->signupUser($user, $formData['password']);
					
					// Create account
					$account->name = $user->fullName;
					$account->planKey = $this->getAccountPlansMapper()->getFreeAccountPlan()->key;
					$this->getAccountsMapper()->createAccount($account, $user);
					
					// Tie user to account
					$this->getUsersMapper()->tieUserToAccount($user, $account);
					
					// Log in
					$user->setPasswordAuthenticated();
					$this->getAuthService()->getStorage()->write($user);
					
					// Post-signup setup
					$this->postSignupSetup();
					
					$this->getUsersMapper()->commitTransaction();
					
					return $this->redirect()->toRoute('home');
					
				} catch (Exception $e) {
					$this->getAuthService()->clearIdentity();
					$this->getUsersMapper()->rollbackTransaction();
					$this->flashMessenger('A problem occurred during the sign up process, please try again');
				}
			}
		}
		
		return array(
			'title'	=> 'Create a Tactile CRM account',
			'form'	=> $form,
		);
	}
	
}
