<?php

namespace Omelettes\Controller;

use Omelettes\Form,
	Omelettes\Model;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter,
	Zend\Http\Header\SetCookie;

class AuthController extends AbstractController
{
	use AuthUsersTrait;
	use AuthFormsTrait;
	
	public function forgotPasswordAction()
	{
		if ($this->getAuthService()->hasIdentity()) {
			// Already logged in
			$this->flashMessenger()->addSuccessMessage('You are already logged in');
			return $this->redirect()->toRoute('home');
		}
		
		$form = $this->getForgotPasswordForm();
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			$form->setInputFilter($this->getForgotPasswordFilter()->getInputFilter());
			$form->setData($request->getPost());
				
			if ($form->isValid()) {
				$emailAddress = $form->getInputFilter()->getValue('name');
				$user = $this->getUsersMapper()->findByName($emailAddress);
				$passwordResetKey = $this->getUsersMapper()->regeneratePasswordResetKey($user);
				$this->sendForgotPasswordEmail($emailAddress, $passwordResetKey);
				$this->flashMessenger()->addSuccessMessage("Instructions for resetting your password have been sent to $emailAddress");
				
				return $this->redirect()->toRoute('login');
			}
		}
		
		return array(
			'form'		=> $form,
		);
	}
	
	public function sendForgotPasswordEmail($emailAddress, $passwordResetKey)
	{
		if (false === ($user = $this->getUsersMapper()->findByName($emailAddress))) {
			throw new \Exception('Failed to find user by name: ' . $emailAddress);
		}
		
		$variables = array(
			'user_key'				=> $user->key,
			'password_reset_key'	=> $passwordResetKey,
		);
		
		$mailer = $this->getServiceLocator()->get('Mailer');
		$mailer->setHtmlTemplate('mail/html/reset-password', $variables);
		$mailer->setTextTemplate('mail/text/reset-password', $variables);
		
		$mailer->send(
			'Subject Text',
			$emailAddress
		);
	}
	
	protected function rememberMe()
	{
		if (!$this->getAuthService()->hasIdentity()) {
			throw new \Exception('Expected an identity');
		}
		$user = $this->getAuthService()->getIdentity();
		$cookieData = $this->getUserLoginsMapper()->saveLogin($user->name);
		$this->getAuthService()->setLoginCookie($this->getResponse(), $cookieData);
	}
	
	public function loginAction()
	{
		if ($this->getAuthService()->hasIdentity()) {
			// Already logged in
			$this->flashMessenger()->addSuccessMessage('You are already logged in');
			return $this->redirect()->toRoute('home');
		}
		$form = $this->getLoginForm();
		
		$request = $this->getRequest();
		$user = new Model\AuthUser();
		$form->bind($user);
		
		if ($request->isPost()) {
			$form->setInputFilter($this->getLoginFilter()->getInputFilter());
			$form->setData($request->getPost());
			
			if ($form->isValid()) {
				$this->getAuthService()->getAdapter()
					->setIdentity($form->getInputFilter()->getValue('name'))
					->setCredential($form->getInputFilter()->getValue('password'));
				
				$result = $this->getAuthService()->authenticate();
				if ($result->isValid()) {
					$userIdentity = new Model\AuthUser((array)$this->getAuthService()->getAdapter()->getResultRowObject());
					
					// We've just authenticated with a password
					$userIdentity->setPasswordAuthenticated();
					$this->getAuthService()->getStorage()->write($userIdentity);
					if ($request->getPost('remember_me')) {
						$this->rememberMe();
					}
					$this->flashMessenger()->addSuccessMessage('Login successful');
					return $this->redirect()->toRoute('home');
				} else {
					$this->flashMessenger()->addErrorMessage('No user was found matching that email address and/or password');
				}
			}
		}
		
		return array(
			'form'      => $form,
		);
	}
	
	protected function unrememberMe()
	{
		$cookie = $this->getRequest()->getCookie();
		if ($cookie && $cookie->offsetExists('login')) {
			$data = $this->getUserLoginsMapper()->splitCookieData($cookie->login);
			$name = $this->getAuthService()->getIdentity()->name;
			$this->getUserLoginsMapper()->deleteForNameWithSeries($name, $data['series']);
			// Remove the login cookie
			$setCookieHeader = new SetCookie(
				'login',
				'',
				(int)date('U', strtotime('-2 weeks'))
			);
			$this->getResponse()->getHeaders()->addHeader($setCookieHeader);
		}
	}
	
	public function logoutAction()
	{
		if ($this->getAuthService()->hasIdentity()) {
			$user = $this->getAuthService()->getIdentity();
			$this->unrememberMe();
			$this->getAuthService()->clearIdentity();
		}
			
		$this->flashMessenger()->addSuccessMessage('You have successfully logged out');
		return $this->redirect()->toRoute('login');
	}
	
	public function resetPasswordAction()
	{
		if ($this->getAuthService()->hasIdentity()) {
			// Already logged in
			$this->flashMessenger()->addSuccessMessage('You are already logged in');
			return $this->redirect()->toRoute('home');
		}
		
		// Create a single-use authentication adapter for authorising against the password reset key
		$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		$dbTableResetPasswordAuthAdapter = new DbTableAuthAdapter(
			$dbAdapter,
			'users',
			'key',
			'password_reset_key',
			"password_reset_requested IS NOT NULL AND password_reset_requested > (now() - interval '1 day') password_reset_key IS NOT NULL AND acl_role != 'system'"
		);
		$dbTableResetPasswordAuthAdapter->setIdentity($this->params('user_key'))
			->setCredential($this->params('password_reset_key'));
		$result = $dbTableResetPasswordAuthAdapter->authenticate();
		if (!$result->isValid()) {
			$this->flashMessenger()->addErrorMessage('Invalid user and/or password reset key');
			return $this->redirect()->toRoute('login');
		}
		$passwordResetUser = new Model\AuthUser((array)$dbTableResetPasswordAuthAdapter->getResultRowObject());
		
		// User has verified password reset key, allow form to be displayed
		$form = $this->getResetPasswordForm();
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			$form->setInputFilter($this->getResetPasswordFilter()->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid()) {
				// We can now change the password
				$this->getUsersMapper()->updatePassword($passwordResetUser, $form->getInputFilter()->getValue('password_new'));
				$this->flashMessenger()->addSuccessMessage('Your password has been updated');
				return $this->redirect()->toRoute('login');
			}
		}
		
		return array(
			'form'					=> $form,
			'user_key'				=> $passwordResetUser->key,
			'password_reset_key'	=> $this->params('password_reset_key'),
		);
	}
	
	public function loginTheftWarningAction()
	{
		return array();
	}
	
}
