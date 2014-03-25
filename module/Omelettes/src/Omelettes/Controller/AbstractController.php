<?php

namespace Omelettes\Controller;

use Omelettes\Form,
	Omelettes\Model;
use Zend\EventManager\EventManagerInterface,
	Zend\Http\Response as HttpResponse,
	Zend\Log\Logger,
	Zend\Mvc\MvcEvent,
	Zend\Mvc\Controller\AbstractActionController,
	Zend\Permissions\Acl,
	Zend\View\Model\JsonModel,
	Zend\View\Model\ViewModel;

abstract class AbstractController extends AbstractActionController
{
	use FormsTrait;
	
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
	
	public function getLogger()
	{
		if (!$this->logger) {
			$logger = $this->getServiceLocator()->get('Omelettes/Logger');
			$this->logger = $logger;
		}
		
		return $this->logger;
	}
	
	/**
	 * Override the default EventManager setter to allow us to specify a pre-dispatch event handler
	 */
	public function setEventManager(EventManagerInterface $events)
	{
		parent::setEventManager($events);
		
		$controller = $this;
		$events->attach(MvcEvent::EVENT_DISPATCH, function ($e) use ($controller) {
			return $controller->preDispatch();
		}, 100);
	}
	
	/**
	 * Can be overriden to specify pre-action controller logic
	 */
	protected function preDispatch()
	{
		return;
	}
	
}
