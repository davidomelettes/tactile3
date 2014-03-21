<?php

namespace Omelettes\Form;

use Zend\Form\Form,
	Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorInterface;

class NamedItemForm extends Form implements ServiceLocatorAwareInterface
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
	
	public function addKeyElement()
	{
		$this->add(array(
			'name'		=> 'key',
			'type'		=> 'Hidden',
		));
	
		return $this;
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
	
	public function addSubmitFieldset($buttonText = 'Save', $buttonClass = 'btn btn-primary')
	{
		$fieldset = new Fieldset\SubmitFieldset();
		$fieldset->addSubmitElement($buttonText, $buttonClass);
		
		// Negative priority should ensure the submit fieldset is always last
		// (unless something else specifies an even lower priority)
		$this->add($fieldset, array('priority' => -1));
	
		return $this;
	}
	
	public function init()
	{
		$this->addNameElement('Name');
	
		$this->addSubmitFieldset();
	}
	
}
