<?php

namespace Tactile\Controller;

use Tactile\Form,
	Tactile\Model;
use Omelettes\Controller\FormsTrait,
	Omelettes\Paginator\Paginator;

trait QuantaTrait
{
	use QuantumResourceTrait;
	
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
	 * @return Model\QuantaMapper
	 */
	public function getQuantaMapper()
	{
		if (!$this->quantaMapper) {
			$quantaMapper = $this->getServiceLocator()->get('Tactile\Model\QuantaMapper');
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
	 * @return Filter\QuantumFilter
	 */
	public function getQuantumFilter()
	{
		return $this->getFilter('Tactile\Form\QuantumFilter');
	}
	
}
