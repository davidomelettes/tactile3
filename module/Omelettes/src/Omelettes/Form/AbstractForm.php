<?php

namespace Omelettes\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait;

abstract class AbstractForm extends Form implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	
	public function __construct($name = null)
	{
		parent::__construct($name);
		$this->setAttribute('method', 'post');
	}
	
	public function getApplicationServiceLocator()
	{
		return $this->getServiceLocator()->getServiceLocator();
	}
	
	public function addSubmitFieldset($buttonText = 'Save', $buttonClass = 'btn btn-success', $buttonLoadingText = null)
	{
		$fieldset = new Fieldset\SubmitFieldset();
		$fieldset->addSubmitElement($buttonText, $buttonClass, $buttonLoadingText);
	
		// Negative priority should ensure the submit fieldset is always last
		// (unless something else specifies an even lower priority)
		$this->add($fieldset, array('name' => 'submit', 'priority' => -1));
	
		return $this;
	}
	
}
