<?php

namespace Tactile\Controller;

use Tactile\Model;
use Omelettes\Controller;

class ContactsController extends QuantaController
{
	use ContactsTrait {
		ContactsTrait::getQuantaMapper as getContactsMapper;
		ContactsTrait::getQuantum as getContact;
		ContactsTrait::getQuantumForm as getContactForm;
		ContactsTrait::getQuantumFilter as getContactFilter;
	}
	
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
	
	protected function findRequestedContact()
	{
		$key = $this->params('key');
		if ($key) {
			$model = $this->getContactsMapper()->find($key);
			if (!$model) {
				$this->flashMessenger()->addErrorMessage('Failed to find Contact with key: ' . $this->params('key'));
			}
			$model->xmlInflate();
			return $model;
		}
		$this->flashMessenger()->addErrorMessage('Missing identifier');
		
		return false;
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
			'title'	=> sprintf('Add a new %s', $this->resource->labelSingular),
			'form'	=> $form,
			'crud'	=> $this->constructNavigation($this->getAddNavigationConfig($model)),
		));
	}
	
	public function viewAction()
	{
		$model = $this->findRequestedContact();
		if (!$model) {
			return $this->redirect()->toRoute($this->getRouteName());
		}
		$this->addPageTitleSegment($model->name);
		
		return $this->returnViewModel(array(
			'title'	=> $model->name,
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
			'title'	=> sprintf('Edit %s', $model->name),
			'model'	=> $model,
			'crud'	=> $this->constructNavigation($this->getEditNavigationConfig($model)),
			'form'	=> $form,
		));
	}
	
}
