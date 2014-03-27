<?php

namespace Tactile\Controller;

use Tactile\Model;
use Omelettes\Controller;

class ContactsController extends QuantaController
{
	use ContactsTrait {
		ContactsTrait::getQuantaMapper as getContactsMapper;
		ContactsTrait::getQuantaPaginator as getContactsPaginator;
		ContactsTrait::getQuantum as getContact;
		ContactsTrait::getQuantumForm as getContactForm;
		ContactsTrait::getQuantumFilter as getContactFilter;
	}
	use Controller\CrudNavigationTrait;
	
	protected function preDispatch()
	{
		// Ensure this controller always has its resource
		if (!$this->getQuantumResource('contacts')) {
			// Something bad happened
			throw new \Exception('Missing contacts resource');
		}
		
		return;
	}
	
	protected function postDispatch()
	{
		$this->addPageTitleSegment('Contacts');
		
		return;
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
	
	public function getViewNavigationConfig(Model\Quantum $model)
	{
		return array(
			array(
				'label'			=> 'Edit',
				'route'			=> $this->getRouteName(),
				'routeOptions'	=> array('action' => 'edit', 'key' => $model->key),
				'icon'			=> 'pencil',
			),
			array(
				'label'			=> 'Delete',
				'route'			=> $this->getRouteName(),
				'routeOptions'	=> array('action' => 'delete', 'key' => $model->key),
				'icon'			=> 'trash',
			),
		);
	}
	
	protected function findRequestedContact()
	{
		$key = $this->params('key');
		if ($key) {
			$model = $this->getContactsMapper()->find($key);
			if (!$model) {
				$this->flashMessenger()->addErrorMessage('Failed to find Contact with key: ' . $this->params('key'));
			}
			$model->inflate();
			return $model;
		}
		$this->flashMessenger()->addErrorMessage('Missing identifier');
		
		return false;
	}
	
	public function indexAction()
	{
		return $this->returnViewModel(array(
			'paginator'	=> $this->getContactsPaginator((int)$this->params()->fromQuery('page', 1)),
			'crud'		=> $this->constructNavigation($this->getIndexNavigationConfig()),
		));
	}
	
	public function addAction()
	{
		$model = $this->getContact();
		$form = $this->getContactForm();
		$form->bind($model);
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setInputFilter($this->getContactFilter()->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid()) {
				$this->getContactsMapper()->createContact($model);
				$this->flashMessenger()->addSuccessMessage('Contact created');
				return $this->redirect()->toRoute($this->getRouteName(), array('action' => 'view', 'key' => $model->key));
			}
		}
		
		return $this->returnViewModel(array(
			'form' => $form,
		));
	}
	
	public function viewAction()
	{
		$model = $this->findRequestedContact();
		if (!$model) {
			return $this->redirect()->toRoute($this->getRouteName());
		}
		$this->addPageTitleSegment($model->name);
		
		return $this->returnViewModel( array(
			'model'	=> $model,
			'crud'	=> $this->constructNavigation($this->getViewNavigationConfig($model)),
		));
	}
	
	public function editAction()
	{
		$model = $this->findRequestedContact();
		if (!$model) {
			return $this->redirect()->toRoute($this->getRouteName());
		}
		$form = $this->getContactForm();
		$form->bind($model);
	
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setInputFilter($this->getContactFilter()->getInputFilter());
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$this->getContactsMapper()->saveContact($model);
				$this->flashMessenger()->addSuccessMessage('Contact created');
				return $this->redirect()->toRoute($this->getRouteName(), array('action' => 'view', 'key' => $model->key));
			}
		}
	
		return $this->returnViewModel(array(
			'model'	=> $model,
			'crud'	=> $this->constructNavigation($this->getViewNavigationConfig($model)),
			'form' => $form,
		));
	}
	
}
