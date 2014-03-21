<?php

namespace TactileAdmin\Controller;

use Omelettes\Controller;
use TactileAdmin\Model;

class ResourcesController extends Controller\AbstractController
{
	use ResourcesTrait;
	use Controller\CrudNavigationTrait;
	
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
	
	public function getViewNavigationConfig(Model\Resource $model)
	{
		return array(
			array(
				'label'			=> 'Edit',
				'route'			=> $this->getRouteName(),
				'routeOptions'	=> array('action' => 'edit', 'resource_name' => $model->name),
				'icon'			=> 'pencil',
			),
			array(
				'label'			=> 'Form Editor',
				'route'			=> $this->getRouteName(),
				'routeOptions'	=> array('action' => 'form-editor', 'resource_name' => $model->name),
				'icon'			=> 'list-alt',
			),
			array(
				'label'			=> 'Delete',
				'route'			=> $this->getRouteName(),
				'routeOptions'	=> array('action' => 'delete', 'resource_name' => $model->name),
				'icon'			=> 'trash',
			),
		);
	}
	
	protected function findRequestedResource()
	{
		$resourceName = $this->params('resource_name');
		if ($resourceName) {
			$model = $this->getResourcesMapper()->findByName($resourceName);
			if (!$model) {
				$this->flashMessenger()->addErrorMessage('Failed to find Resource with name: ' . $resourceName);
			}
			return $model;
		}
		$this->flashMessenger()->addErrorMessage('Missing identifier');
	
		return false;
	}
	
	public function indexAction()
	{
		return $this->returnViewModel(array(
			'paginator'	=> $this->getResourcesPaginator((int)$this->params()->fromQuery('page', 1)),
			'crud'		=> $this->constructNavigation($this->getIndexNavigationConfig()),
		));
	}
	
	public function addAction()
	{
		$model = $this->getResource();
		$form = $this->getAddResourceForm();
		$form->bind($model);
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setInputFilter($this->getResourceFilter()->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid()) {
				$this->getResourcesMapper()->createUnprotectedResource($model);
				$this->flashMessenger()->addSuccessMessage('Resource created');
				return $this->redirect()->toRoute($this->getRouteName(), array('action' => 'view', 'resource_name' => $model->name));
			}
		}
		
		return $this->returnViewModel(array(
			'form'	=> $form,
		));
	}
	
	public function formEditAction()
	{
		
	}
	
	public function viewAction()
	{
		$model = $this->findRequestedResource();
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
		$model = $this->findRequestedResource();
		if (!$model) {
			return $this->redirect()->toRoute($this->getRouteName());
		}
		$key = $model->key;
		
		$form = $model->protected ? $this->getEditProtectedResourceForm() : $this->getEditUnprotectedResourceForm();
		$form->bind($model);
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setInputFilter($this->getResourceFilter()->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid()) {
				$model->key = $key;
				$model->name = $this->params('resource_name');
				$this->getResourcesMapper()->updateResource($model);
				$this->flashMessenger()->addSuccessMessage('Resource edited');
				return $this->redirect()->toRoute($this->getRouteName(), array('action' => 'view', 'resource_name' => $model->name));
			}
		}
		
		return $this->returnViewModel( array(
			'model'	=> $model,
			'crud'	=> $this->constructNavigation($this->getViewNavigationConfig($model)),
			'form'	=> $form,
		));
	}
	
	public function formEditorAction()
	{
		$model = $this->findRequestedResource();
		if (!$model) {
			return $this->redirect()->toRoute($this->getRouteName());
		}
		
		$form = $this->getResourceMetaForm();
		$form->bind($model);
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setInputFilter($this->getResourceFilter()->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid()) {
				$this->getResourcesMapper()->createUnprotectedResource($model);
				$this->flashMessenger()->addSuccessMessage('Resource edited');
				return $this->redirect()->toRoute($this->getRouteName(), array('action' => 'view', 'resource_name' => $model->name));
			}
		}
		
		return $this->returnViewModel( array(
			'model'	=> $model,
			'crud'	=> $this->constructNavigation($this->getViewNavigationConfig($model)),
			'form'	=> $form,
		));
	}
	
}

