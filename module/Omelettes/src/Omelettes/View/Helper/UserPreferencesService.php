<?php

namespace Omelettes\View\Helper;

use Omelettes\Service\UserPreferencesService;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait,
	Zend\View\Helper\AbstractHelper;

class UserPreferencesService extends AbstractHelper implements ServiceLocatorAwareInterface 
{
	use ServiceLocatorAwareTrait;
	
	/**
	 * @var UserPreferencesService
	 */
	protected $userPreferencesService;
	
	public function __invoke()
	{
		return $this->getUserPreferencesService();
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
	 * @return UserPreferencesService
	 */
	public function getUserPreferencesService()
	{
		if (!$this->userPreferencesService) {
			$this->userPreferencesService = $this->getApplicationServiceLocator()->get('UserPreferencesService');
		}
		
		return $this->userPreferencesService;
	}
	
}
