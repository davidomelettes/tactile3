<?php

namespace TactileAdmin\Controller;

use Omelettes\Controller,
	Omelettes\Model;
use TactileAdmin\Form;
use Zend\Form\FormInterface;

class UsersController extends Controller\AbstractController
{
	use Controller\AuthUsersTrait;
	use Controller\CrudNavigationTrait;
	
	/**
	 * @var Model\AuthUser
	 */
	protected $user;
	
	/**
	 * @var Form\AddUserForm
	 */
	protected $addUserForm;
	
	/**
	 * @var Form\AddUserFilter
	 */
	protected $addUserFilter;
	
	public function getUser()
	{
		if (!$this->user) {
			$model = new Model\AuthUser();
			$this->user = $model;
		}
		
		return $this->user;
	}
	
	public function getAddUserForm()
	{
		if (!$this->addUserForm) {
			$form = $this->getServiceLocator()->get('FormElementManager')->get('TactileAdmin\Form\AddUserForm');
			$this->addUserForm = $form;
		}
		
		return $this->addUserForm;
	}
	
	public function getAddUserFilter()
	{
		if (!$this->addUserFilter) {
			$filter = $this->getServiceLocator()->get('TactileAdmin\Form\AddUserFilter');
			$this->addUserFilter = $filter;
		}
		
		return $this->addUserFilter;
	}
	
	public function getIndexNavigationConfig()
	{
		return array(
			array(
				'label'			=> 'Add',
				'route'			=> $this->getRouteName(),
				'routeOptions'	=> array('action' => 'add'),
				'icon'			=> 'plus',
			),
		);
	}
	
	public function getViewNavigationConfig(Model\Contact $model)
	{
		return array(
			array(
				'label'			=> 'Edit',
				'route'			=> $this->getRouteName(),
				'routeOptions'	=> array('action' => 'edit', 'key' => $model->key),
				'icon'			=> 'pencil',
			),
			array(
				'label'			=> 'Disable',
				'route'			=> $this->getRouteName(),
				'routeOptions'	=> array('action' => 'disable', 'key' => $model->key),
				'icon'			=> 'trash',
			),
		);
	}
	
	public function indexAction()
	{
		return $this->returnViewModel(array(
			'paginator'	=> $this->getUsersPaginator((int)$this->params()->fromQuery('page', 1)),
			'crud'		=> $this->constructNavigation($this->getIndexNavigationConfig()),
		));
	}
	
	public function addAction()
	{
		// Check plan
		
		// Redirect to plan upgrade
		
		// Check user limit
		
		// Redirect to limit upgrade
		
		$user = $this->getUser();
		$form = $this->getAddUserForm();
		$form->bind($user);
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setInputFilter($this->getAddUserFilter()->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid()) {
				$formData = $form->getData(FormInterface::VALUES_AS_ARRAY);
				$plaintextPassword = $formData['password'];
				if (empty($password)) {
					$plaintextPassword = $this->getUsersMapper()->generateRandomPassword();
				}
				
				$this->getUsersMapper()->beginTransaction();
				try {
					$this->getUsersMapper()->signupUser($user, $plaintextPassword);
					$account = new Model\Account(array('key' => $this->getAuthService()->getIdentity()->accountKey));
					$this->getUsersMapper()->tieUserToAccount($user, $account);
					
					// Send welcome email
					$variables = array(
						'fullName'		=> $user->fullName,
						'senderName'	=> $this->getAuthService()->getIdentity()->fullName,
						'username'		=> $user->name,
						'password'		=> $plaintextPassword,
					);
						
					$mailer = $this->getServiceLocator()->get('Mailer');
					$mailer->setHtmlTemplate('mail/html/add-user', $variables);
					$mailer->setTextTemplate('mail/text/add-user', $variables);
						
					$mailer->send(
						'Welcome to Tactile CRM',
						$user->name
					);
					
					$this->getUsersMapper()->commitTransaction();
					
				} catch (\Exception $e) {
					$this->getUsersMapper()->rollbackTransaction();
					throw $e;
					//$this->flashMessenger()->addErrorMessage('An error occurred during signup, the user was not created');
					//return $this->redirect()->toRoute($this->getRouteName(), array('action' => 'index'));
				}
				
				$this->flashMessenger()->addSuccessMessage('User created');
				return $this->redirect()->toRoute($this->getRouteName(), array('action' => 'view', 'key' => $user->key));
			}
		}
		
		return $this->returnViewModel(array(
			'form' => $form,
		));
	}
	
}
