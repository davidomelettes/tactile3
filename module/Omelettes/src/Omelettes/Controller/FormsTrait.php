<?php

namespace Omelettes\Controller;

trait FormsTrait
{
	protected $traitForms = array();
	
	protected $traitFilters = array();
	
	/**
	 * Returns an instance of the requested form, instantiated through FormElementManager
	 * 
	 * @var string $className
	 */
	public function getForm($className)
	{
		if (!isset($this->traitForms[$className])) {
			$form = $this->getServiceLocator()->get('FormElementManager')->get($className);
			$this->traitForms[$className] = $form;
		}
		
		return $this->traitForms[$className];
	}
	
	/**
	 * Returns an instance of the requested filter
	 *
	 * @var string $className
	 */
	public function getFilter($className)
	{
		if (!isset($this->traitFilters[$className])) {
			$filter = $this->getServiceLocator()->get($className);
			$this->traitFilters[$className] = $filter;
		}
		
		return $this->traitFilters[$className];
	}
	
}
