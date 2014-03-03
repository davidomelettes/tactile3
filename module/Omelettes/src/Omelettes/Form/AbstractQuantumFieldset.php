<?php

namespace Omelettes\Form;

use Zend\Form\Fieldset,
	Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractQuantumFieldset extends Fieldset implements ServiceLocatorAwareInterface
{
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $serviceLocator;
	
	public function __construct($name = null)
	{
		parent::__construct($name);
		$this->setAttribute('method', 'post');
	}
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
	}
	
	public function getServiceLocator()
	{
		return $this->serviceLocator;
	}
	
	public function getApplicationServiceLocator()
	{
		return $this->getServiceLocator()->getServiceLocator();
	}
	
	public function addNameElement($label = 'Name')
	{
		$this->add(array(
			'name'		=> 'name',
			'type'		=> 'Text',
			'options'	=> array(
				'label'		=> $label,
			),
			'attributes'=> array(
				'id'		=> $this->getName() . 'Name',
			),
		));
	
		return $this;
	}
	
}
