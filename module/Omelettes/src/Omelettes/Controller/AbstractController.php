<?php

namespace Omelettes\Controller;

use Omelettes\Form;
use Omelettes\Model;
use OmelettesAuth\Authentication\AuthenticationService;
use OmelettesLocale\Model\LocalesMapper;
use Zend\Permissions\Acl,
	Zend\Log\Logger,
	Zend\Mvc\Controller\AbstractActionController,
	Zend\View\Model\JsonModel,
	Zend\View\Model\ViewModel;

abstract class AbstractController extends AbstractActionController
{
	protected $acceptCriteria = array(
		'Zend\View\Model\ViewModel' => array(
			'text/html',
		),
		'Zend\View\Model\JsonModel' => array(
			'application/json',
		),
	);
	
	/**
	 * @var Acl\Acl
	 */
	protected $aclService;
	
	/**
	 * @var AuthenticationService
	 */
	protected $authService;
	
	/**
	 * @var LocalesMapper
	 */
	protected $localesMapper;
	
	/**
	 * @var Logger
	 */
	protected $logger;
	
	/**
	 * Returns a view model selected by the HTTP Accept header criteria
	 * 
	 * @param array $variables
	 * @return ViewModel
	 */
	public function returnViewModel(array $variables = array())
	{
		$viewModel = $this->acceptableViewModelSelector($this->acceptCriteria);
		$viewModel->setVariables($variables, true);
		
		return $viewModel;
	}
	
	public function getRouteName()
	{
		$event = $this->getEvent();
		$routeMatch = $event->getRouteMatch();
		
		return $routeMatch->getMatchedRouteName();
	}
	
	public function getAclService()
	{
		if (!$this->aclService) {
			$aclService = $this->getServiceLocator()->get('AclService');
			$this->aclService = $aclService;
		}
		
		return $this->aclService;
	}
	
	public function getAclRole()
	{
		return $this->getAuthService()->hasIdentity() ? $this->getAuthService()->getIdentity()->aclRole : 'guest';
	}
	
	public function getAuthService()
	{
		if (!$this->authService) {
			$authService = $this->getServiceLocator()->get('AuthService');
			$this->authService = $authService;
		}
		
		return $this->authService;
	}
	
	public function getLocalesMapper()
	{
		if (!$this->localesMapper) {
			$localesMapper = $this->getServiceLocator()->get('OmelettesLocale\Model\LocalesMapper');
			$this->localesMapper = $localesMapper;
		}
		
		return $this->localesMapper;
	}
	
	public function getLogger()
	{
		if (!$this->logger) {
			$logger = $this->getServiceLocator()->get('Omelettes/Logger');
			$this->logger = $logger;
		}
		
		return $this->logger;
	}
	
}
