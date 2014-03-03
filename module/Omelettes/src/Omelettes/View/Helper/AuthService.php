<?php

namespace Omelettes\View\Helper;

use Zend\Authentication\AuthenticationService,
	Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait,
	Zend\View\Helper\AbstractHelper;

class AuthService extends AbstractHelper implements ServiceLocatorAwareInterface 
{
	use ServiceLocatorAwareTrait;
	
	/**
	 * @var AuthenticationService
	 */
	protected $authService;
	
	public function __invoke()
	{
		return $this->getAuthService();
	}
	
	/**
	 * Returns the application service manager
	 * 
	 * @return ServiceLocatorInterface
	 */
	public function getApplicationServiceLocator()
	{
		// View helpers implementing ServiceLocatorAwareInterface are given Zend\View\HelperPluginManager!
		return $this->getServiceLocator()->getServiceLocator();
	}
	
	/**
	 * Returns the authentication service used by the application
	 * 
	 * @return AuthenticationService
	 */
	public function getAuthService()
	{
		if (!$this->authService) {
			$this->authService = $this->getApplicationServiceLocator()->get('AuthService');
		}
		
		return $this->authService;
	}
	
}
