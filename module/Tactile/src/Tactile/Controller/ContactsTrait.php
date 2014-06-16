<?php

namespace Tactile\Controller;

use Tactile\Form,
	Tactile\Model;
use Omelettes\Paginator\Paginator;

trait ContactsTrait
{
	use QuantaTrait;
	
	/**
	 * @return Model\ContactsMapper
	 */
	public function getQuantaMapper()
	{
		if (!$this->quantaMapper) {
			$mapper = $this->getServiceLocator()->get('Tactile\Model\ContactsMapper');
			$this->quantaMapper = $mapper;
		}
		
		return $this->quantaMapper;
	}
	
	
	/**
	 * @return Model\Contact
	 */
	public function getQuantum()
	{
		if (!$this->quantum) {
			$model = $this->getServiceLocator()->get('Tactile\Model\Contact');
			$model->setResource($this->getQuantumResource());
			$this->quantum = $model;
		}
		
		return $this->quantum;
	}
	
}
