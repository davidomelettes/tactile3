<?php

namespace Tactile\Controller;

use Tactile\Form,
	Tactile\Model;
use Omelettes\Controller\FormsTrait,
	Omelettes\Paginator\Paginator;

trait QuantaTrait
{
	/**
	 * @var Model\ResourcesMapper
	 */
	protected $resourcesMapper;
	
	/**
	 * @var Model\Resource
	 */
	protected $resource;
	
	/**
	 * @var Model\QuantaMapper
	 */
	protected $quantaMapper;
	
	/**
	 * @var Paginator
	 */
	protected $quantaPaginator;
	
	/**
	 * @var Model\Quantum
	 */
	protected $quantum;
	
	/**
	 * @var Form\QuantumForm
	 */
	protected $quantumForm;
	
	/**
	 * @var Form\QuantumFilter
	 */
	protected $quantumFilter;
	
	/**
	 * @return Model\ResourcesMapper
	 */
	public function getResourcesMapper()
	{
		if (!$this->resourcesMapper) {
			$resourcesMapper = $this->getServiceLocator()->get('Tactile\Model\ResourcesMapper');
			$this->resourcesMapper = $resourcesMapper;
		}
	
		return $this->resourcesMapper;
	}
	
	/**
	 * @return Model\Resource
	 */
	protected function getQuantumResource($nameOverride = null)
	{
		if ($this->resource) {
			return $this->resource;
		}
	
		$name = $nameOverride ? $nameOverride : $this->params('resource_name');
		if ($name) {
			$model = $this->getResourcesMapper()->findByName($name);
			if (!$model) {
				// Invalid route, should handle as 404
				return false;
			}
			$this->resource = $model;
			return $this->resource;
		}
	
		return false;
	}
	
	/**
	 * @return Model\QuantaMapper
	 */
	public function getQuantaMapper()
	{
		if (!$this->quantaMapper) {
			$quantaMapper = $this->getServiceLocator()->get('Tactile\Model\QuantaMapper');
			$quantaMapper->setResource($this->getQuantumResource());
			$this->quantaMapper = $quantaMapper;
		}
		
		return $this->quantaMapper;
	}
	
	/**
	 * @return Paginator
	 */
	public function getQuantaPaginator($page = 1)
	{
		if (!$this->quantaPaginator) {
			$quantaPaginator = $this->getQuantaMapper()->fetchAll(true);
			$quantaPaginator->setCurrentPageNumber($page);
			$this->quantaPaginator = $quantaPaginator;
		}
	
		return $this->quantaPaginator;
	}
	
	/**
	 * @return Model\Quantum
	 */
	public function getQuantum()
	{
		if (!$this->quantum) {
			$model = new Model\Quantum();
			$model->setResource($this->getQuantumResource());
			$this->quantum = $model;
		}
		
		return $this->quantum;
	}
	
	/**
	 * @return Form\QuantumForm
	 */
	public function getQuantumForm()
	{
		$form = $this->getForm('Tactile\Form\QuantumForm');
		$form->setResource($this->getQuantumResource()); 
		return $form;
	}
	
	/**
	 * @return Form\QuantumFilter
	 */
	public function getQuantumFilter()
	{
		$filter = $this->getFilter('Tactile\Form\QuantumFilter');
		$filter->setResource($this->getQuantumResource());
		return $filter;
	}
	
}
