<?php

namespace Tactile\Controller;

use Tactile\Model;
use Omelettes\Controller;

class QuantaController extends Controller\AbstractController
{
	use QuantaTrait;
	use Controller\CrudNavigationTrait;
	
	protected function preDispatch()
	{
		// Ensure this controller always has its resource
		if (!$this->getQuantumResource()) {
			// Unable to load a resource, handle as 404
			return $this->notFoundAction();
		}
		
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
				'label'			=> 'Delete',
				'route'			=> $this->getRouteName(),
				'routeOptions'	=> array('action' => 'delete', 'key' => $model->key),
				'icon'			=> 'trash',
			),
		);
	}
	
	public function indexAction()
	{
		return $this->returnViewModel(array(
			'paginator'	=> $this->getQuantaPaginator((int)$this->params()->fromQuery('page', 1)),
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
