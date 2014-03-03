<?php

namespace Omelettes\View\Helper;

use Zend\Permissions\Acl,
	Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait,
	Zend\View\Helper\AbstractHelper;

class AclService extends AbstractHelper implements ServiceLocatorAwareInterface 
{
	use ServiceLocatorAwareTrait;
	
	/**
	 * @var Acl\Acl
	 */
	protected $acl;
	
	public function __invoke()
	{
		return $this;
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
	 * @return Acl\Acl
	 */
	public function getAcl()
	{
		if (!$this->acl) {
			$this->acl = $this->getApplicationServiceLocator()->get('AclService');
		}
		
		return $this->acl;
	}
	
	public function getRole()
	{
		$auth = $this->getApplicationServiceLocator()->get('AuthService');
		
		return $auth->hasIdentity() ? $auth->getIdentity()->aclRole : 'guest';
	}
	
}
