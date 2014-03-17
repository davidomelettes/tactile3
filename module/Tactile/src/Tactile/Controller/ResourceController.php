<?php

namespace Tactile\Controller;

use Omelettes\Model;
use Omelettes\Controller;

class ResourceController extends Controller\AbstractController
{
	use ResourceTrait;
	use Controller\CrudNavigationTrait;
	
	public function getIndexNavigationConfig()
	{
		return array(
			array(
				'label'			=> 'Add',
				'route'			=> $this->getRouteName(),
				'routeOptions'	=> array('resource_name' => $this->resource->name, 'action' => 'add'),
				'icon'			=> 'plus',
			),
		);
	}
	
	public function getViewNavigationConfig(Model\AccountBoundQuantumModel $model)
	{
		return array(
			array(
				'label'			=> 'Edit',
				'route'			=> $this->getRouteName(),
				'routeOptions'	=> array('resource_name' => $this->resource->name, 'action' => 'edit', 'key' => $model->key),
				'icon'			=> 'pencil',
			),
			array(
				'label'			=> 'Delete',
				'route'			=> $this->getRouteName(),
				'routeOptions'	=> array('resource_name' => $this->resource->name, 'action' => 'delete', 'key' => $model->key),
				'icon'			=> 'trash',
			),
		);
	}
	
	public function indexAction()
	{
		if (!$this->findRequestedResource()) {
			return $this->createHttpNotFoundModel($this->getResponse());
		}
		
		return $this->returnViewModel(array(
			//'paginator'	=> $this->getContactsPaginator((int)$this->params()->fromQuery('page', 1)),
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
	
}
