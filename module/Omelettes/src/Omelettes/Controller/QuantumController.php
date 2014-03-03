<?php

namespace Omelettes\Controller;

use Omelettes\Form,
	Omelettes\Model,
	Omelettes\Paginator\Paginator;
use Zend\View\Model\JsonModel,
	Zend\Navigation;

abstract class QuantumController extends AbstractController
{
	/**
	 * @var Form\AbstractQuantumForm
	 */
	protected $addQuantumForm;
	
	protected $addQuantumFormClass;
	
	/**
	 * @var Form\AbstractQuantumFilter
	 */
	protected $addQuantumFilter;
	
	protected $addQuantumFilterClass;
	
	/**
	 * @var Form\AbstractQuantumForm
	 */
	protected $editQuantumForm;
	
	protected $editQuantumFormClass;
	
	/**
	 * @var Form\AbstractQuantumFilter
	 */
	protected $editQuantumFilter;
	
	protected $editQuantumFilterClass;
	
	/**
	 * @var Form\ConfirmDeleteForm
	 */
	protected $deleteQuantumForm;
	
	/**
	 * @var Model\QuantumMapper
	 */
	protected $quantumMapper;
	
	protected $quantumMapperClass;
	
	/**
	 * @var Model\QuantumModel
	 */
	protected $quantumModel;
	
	protected $quantumModelClass;
	
	/**
	 * @var Paginator
	 */
	protected $paginator;
	
	/**
	 * @return Model\QuantumMapper
	 */
	public function getQuantumMapper()
	{
		if (!$this->quantumMapperClass) {
			throw new \Exception('quantumMapperClass must be defined');
		}
		if (!$this->quantumMapper) {
			$mapper = $this->getServiceLocator()->get($this->quantumMapperClass);
			$this->quantumMapper = $mapper;
		}
		
		return $this->quantumMapper;
	}
	
	/**
	 * @return Model\QuantumModel
	 */
	public function getQuantumModel()
	{
		if (!$this->quantumModelClass) {
			throw new \Exception('quantumModelClass must be defined');
		}
		
		if (!$this->quantumModel) {
			$model = new $this->quantumModelClass;
			$this->quantumModel = $model;
		}
		
		return $this->quantumModel;
	}
	
	public function getAddQuantumForm()
	{
		if (!$this->addQuantumFormClass) {
			throw new \Exception('addQuantumFormClass must be defined');
		}
		if (!$this->addQuantumForm) {
			$form = $this->getServiceLocator()->get('FormElementManager')->get($this->addQuantumFormClass);
			$this->addQuantumForm = $form;
		}
		
		return $this->addQuantumForm;
	}
	
	public function getAddQuantumFilter()
	{
		if (!$this->addQuantumFilterClass) {
			throw new \Exception('addQuantumFilterClass must be defined');
		}
		if (!$this->addQuantumFilter) {
			$filter = $this->getServiceLocator()->get($this->addQuantumFilterClass);
			$this->addQuantumFilter = $filter;
		}
		
		return $this->addQuantumFilter;
	}
	
	public function getEditQuantumForm()
	{
		if (!$this->editQuantumFormClass) {
			throw new \Exception('editQuantumFormClass must be defined');
		}
		if (!$this->editQuantumForm) {
			$form = $this->getServiceLocator()->get('FormElementManager')->get($this->editQuantumFormClass);
			$this->editQuantumForm = $form;
		}
		
		return $this->editQuantumForm;
	}
	
	public function getEditQuantumFilter()
	{
		if (!$this->editQuantumFilterClass) {
			throw new \Exception('editQuantumFilterClass must be defined');
		}
		if (!$this->editQuantumFilter) {
			$filter = $this->getServiceLocator()->get($this->editQuantumFilterClass);
			$this->editQuantumFilter = $filter;
		}
		
		return $this->editQuantumFilter;
	}
	
	public function getDeleteQuantumForm($model = null, $route = null, $routeOptions = array())
	{
		if (!$this->deleteQuantumForm) {
			$form = new Form\ConfirmDeleteForm();
			if ($model instanceof Model\QuantumModel) {
				$submitFieldset = $form->get('submit');
				$submitFieldset->add(array(
					'name'		=> 'cancel',
					'type'		=> 'Omelettes\Form\Element\Url',
					'attributes'=> array(
						'value'		=> 'Cancel',
					),
					'options' => array(
						'route' => $route,
						'route_options' => $routeOptions,
						'anchor_class' => 'btn btn-default',
					),
				));
			}
			$this->deleteQuantumForm = $form;
		}
	
		return $this->deleteQuantumForm;
	}
	
	public function getQuantumPaginator()
	{
		if (!$this->paginator) {
			$paginator = $this->getQuantumMapper()->fetchAll(true);
			$paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
			$this->paginator = $paginator;
		}
		
		return $this->paginator;
	}
	
	protected function findRequestedModel()
	{
		// Do we have a slug?
		$slug = $this->params('slug');
		if ($slug) {
			$model = $this->getQuantumMapper()->findBySlug($slug);
			if (!$model) {
				$this->flashMessenger()->addErrorMessage('Failed to find record with slug: ' . $this->params('slug'));
			}
			return $model;
		}
		
		// Do we have a key?
		$key = $this->params('key');
		if ($key) {
			$model = $this->getQuantumMapper()->find($key);
			if (!$model) {
				$this->flashMessenger()->addErrorMessage('Failed to find record with key: ' . $this->params('key'));
			}
			return $model;
		}
		
		$this->flashMessenger()->addErrorMessage('Missing identifier');
		
		return false;
	}
	
	public function addAction()
	{
		if ($this->getQuantumMapper()->isReadOnly() || !$this->addQuantumFormClass) {
			$this->flashMessenger()->addErrorMessage('You do not have permission to do that');
			return $this->redirect()->toRoute($this->getRouteName());
		}
		
		$model = $this->getQuantumModel();
		$form = $this->getAddQuantumForm();
		$form->bind($model);
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setInputFilter($this->getAddQuantumFilter()->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid()) {
				$this->getQuantumMapper()->createQuantum($model);
				$this->flashMessenger()->addSuccessMessage('Record created');
				return $this->redirect()->toRoute($this->getRouteName(), array('action' => 'view', 'key' => $model->key));
			}
		}
		
		return $this->returnViewModel(array(
			'form' => $form,
		));
	}
	
	public function viewAction()
	{
		$model = $this->findRequestedModel();
		if (!$model) {
			return $this->redirect()->toRoute($this->getRouteName());
		}
		
		return $this->returnViewModel( array(
			'model'	=> $model,
			'crud'	=> $this->constructNavigation($this->getViewNavigationConfig($model)),
		));
	}
	
	public function indexAction()
	{
		return $this->returnViewModel(array(
			'paginator'	=> $this->getQuantumPaginator(),
			'crud'		=> $this->constructNavigation($this->getIndexNavigationConfig()),
		));
	}
	
	public function editAction()
	{
		if ($this->getQuantumMapper()->isReadOnly() || !$this->editQuantumFormClass) {
			$this->flashMessenger()->addErrorMessage('You do not have permission to do that');
			return $this->redirect()->toRoute($this->getRouteName());
		}
		
		$key = $this->params('key');
		$model = $this->getQuantumMapper()->find($key);
		if (!$model) {
			$this->flashMessenger()->addErrorMessage('Failed to find record with key: ' . $this->params('key'));
			return $this->redirect()->toRoute($this->getRouteName());
		}
		$form = $this->getEditQuantumForm();
		$form->bind($model);
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setInputFilter($this->getEditQuantumFilter()->getInputFilter());
			$form->setData($request->getPost());
		
			if ($form->isValid()) {
				// Model has been hydrated, no non-form properties will be set
				$model->key = $key;
				$this->getQuantumMapper()->updateQuantum($model);
				$this->flashMessenger()->addSuccessMessage('Record updated');
				return $this->redirect()->toRoute($this->getRouteName(), array('action' => 'view', 'key' => $model->key));
			}
		}
		
		return $this->returnViewModel(array(
			'form'	=> $form,
			'model'	=> $model,
			'crud'	=> $this->constructNavigation($this->getViewNavigationConfig($model)),
		));
	}
	
	public function deleteAction()
	{
		if ($this->getQuantumMapper()->isReadOnly() || !$this->deleteQuantumFormClass) {
			$this->flashMessenger()->addErrorMessage('You do not have permission to do that');
			return $this->redirect()->toRoute($this->getRouteName());
		}
		
		$model = $this->findRequestedModel();
		if (!$model) {
			return $this->redirect()->toRoute($this->getRouteName());
		}
		
		$form = $this->getDeleteQuantumForm($model, $this->getRouteName(), array('action' => 'view', 'key' => $model->key));
		$request = $this->getRequest();
		if ($request->isPost()) {
			$this->getQuantumMapper()->deleteQuantum($model);
			$this->flashMessenger()->addSuccessMessage($model->name . ' deleted');
			return $this->redirect()->toRoute($this->getRouteName());
		}
		
		return $this->returnViewModel(array(
			'model'	=> $model,
			'form'	=> $form,
			'crud'	=> $this->constructNavigation($this->getViewNavigationConfig($model)),
		));
	}
	
	public function autocompleteAction()
	{
		$viewModel = $this->returnViewModel();
		if (!$viewModel instanceof JsonModel) {
			$this->flashMessenger()->addErrorMessage('Accept: application/json only');
			return $this->redirect()->toRoute($this->getRouteName());
		}
		
		$term = $this->params()->fromQuery('term', '');
		$results = array();
		$paginator = $this->getQuantumMapper()->fetchAllWhereNameLike($term, true);
		foreach ($paginator as $quantum) {
			$results[] = array(
				'label' => $quantum->name,
				'value' => $quantum->key,
			);
		}
		if (empty($results)) {
			$results[] = array(
				'label' => sprintf("No Results for '%s'", $term),
				'value' => '',
			);
		}
		$viewModel->setVariables($results);
		
		return $viewModel;
	}
	
	public function processAction()
	{
		if ($this->getQuantumMapper()->isReadOnly() || !$this->editQuantumFormClass) {
			$this->flashMessenger()->addErrorMessage('You do not have permission to do that');
			return $this->redirect()->toRoute($this->getRouteName());
		}
		
		$request = $this->getRequest();
		if (!$request->isPost()) {
			$this->flashMessenger()->addErrorMessage('POST requests only');
			return $this->redirect()->toRoute($this->getRouteName());
		}
		
		$keys = $this->params()->fromPost('keys', array());
		$action = $this->params()->fromPost('action', null);
		$data = $this->params()->fromPost('data', array());
		
		if (empty($keys)) {
			$this->flashMessenger()->addErrorMessage('Nothing selected');
			return $this->redirect()->toRoute($this->getRouteName());
		}
		
		try {
			$successCount = $this->getQuantumMapper()->processQuanta($keys, $action, $data);
		} catch (Model\Exception\UnknownProcessActionException $e) {
			$this->flashMessenger()->addErrorMessage($e->getMessage());
			return $this->redirect()->toRoute($this->getRouteName());
		}
		switch ($action) {
			case 'delete':
				$this->flashMessenger()->addErrorMessage($successCount . ' item' . ($successCount === 1 ? '' : 's') . ' deleted');
				break;
			default:
				$this->flashMessenger()->addErrorMessage('Huh?');
		}
		
		return $this->redirect()->toRoute($this->getRouteName());
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
	
	public function getViewNavigationConfig(Model\QuantumModel $model)
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
	
	public function constructNavigation($config)
	{
		$factory = new Navigation\Service\ConstructedNavigationFactory($config);
		$navigation = $factory->createService($this->getServiceLocator());
		
		return $navigation;
	}
	
}
