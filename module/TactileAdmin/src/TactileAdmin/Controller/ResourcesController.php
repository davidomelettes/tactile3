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
		//$this->getResourcesMapper()->beginTransaction();
		//$this->getResourcesMapper()->installSystemResources();
		//$this->getResourcesMapper()->commitTransaction();
		
		return $this->returnViewModel(array(
			'paginator'	=> $this->getResourcesPaginator((int)$this->params()->fromQuery('page', 1)),
			'crud'		=> $this->constructNavigation($this->getIndexNavigationConfig()),
		));
	}
	
	public function addAction()
	{
		$model = $this->getResource();
		$form = $this->getResourceForm();
		$form->bind($model);
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setInputFilter($this->getResourceFilter()->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid()) {
				//try {
					$this->getResourcesMapper()->createUnprotectedResource($model);
					$this->flashMessenger()->addSuccessMessage('Resource created');
					return $this->redirect()->toRoute($this->getRouteName(), array('action' => 'view', 'resource_name' => $model->name));
				/*
				} catch (\Exception $e) {
					$this->flashMessenger()->addErrorMessage($e->getMessage());
					throw $e
				}
				*/
			}
		}
		
		return $this->returnViewModel(array(
			'form' => $form,
		));
	}
	
	public function formEditAction()
	{
		
	}
	
}

