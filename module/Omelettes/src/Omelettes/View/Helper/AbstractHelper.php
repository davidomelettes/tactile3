<?php

namespace Omelettes\View\Helper;

use Zend\Form\View\Helper\AbstractHelper as ZendHelper,
	Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait,
	Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractHelper extends ZendHelper implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $applicationServiceLocator;
	
	public function getApplicationServiceLocator()
	{
		if (!$this->applicationServiceLocator) {
			$sl = $this->getServiceLocator()->getServiceLocator();
			$this->applicationServiceLocator = $sl;
		}
		return $this->applicationServiceLocator;
	}
	
}
