<?php

namespace Omelettes;

use Omelettes\Authentication\AuthenticationService,
	Omelettes\Session\SaveHandler\DbTableGateway as SessionSaveHandlerDb;
use Zend\Authentication,
	Zend\Console\Adapter\AdapterInterface as Console,
	Zend\Console\Request as ConsoleRequest,
	Zend\Db\ResultSet\ResultSet,
	Zend\Db\TableGateway\TableGateway,
	Zend\Log,
	Zend\ModuleManager\Feature\ConsoleBannerProviderInterface,
	Zend\ModuleManager\Feature\ConsoleUsageProviderInterface,
	Zend\Mvc\MvcEvent,
	Zend\Permissions\Acl,
	Zend\Session;

class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
{
	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__					=> __DIR__ . '/src/' . __NAMESPACE__,
					__NAMESPACE__.'Migration'		=> 'migrations',
				),
			),
		);
	}
	
	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}
	
	public function getServiceConfig()
	{
		return array(
			'aliases'		=> array(
				'Mailer'		=> 'Omelettes\Mailer',
				'Logger'		=> 'Omelettes\Logger',
			),
			'factories' => array(
				// ACL
				'AclService' => function($sm) {
					$acl = new Acl\Acl();
					$config = $sm->get('config');
					if (is_array($config) && isset($config['acl'])) {
						$config = $config['acl'];
						if (is_array($config) && isset($config['roles'])) {
							$roles = $config['roles'];
							foreach ($roles as $role => $roleParents) {
								$role = new Acl\Role\GenericRole($role);
								$acl->addRole($role, $roleParents);
							}
						}
						if (is_array($config) && isset($config['resources'])) {
							$resources = $config['resources'];
							foreach ($resources as $role => $roleResources) {
								foreach ($roleResources as $resource => $privileges) {
									if (!$acl->hasResource($resource)) {
										$acl->addResource(new Acl\Resource\GenericResource($resource));
									}
									$acl->allow($role, $resource, $privileges);
								}
							}
						}
					}
				
					return $acl;
				},
				
				// Authentication
				'Omelettes\Storage\Session' => function($sm) {
					return new Storage\Session(Storage\Session::STORAGE_NAMESPACE);
				},
				'AuthService' => function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$authAdatper = new Authentication\Adapter\DbTable(
						$dbAdapter,
						'users',
						'name',
						'password_hash',
						"sha256(?||salt) AND acl_role != 'system'"
					);
				
					$authService = new AuthenticationService();
					$authService->setAdapter($authAdatper);
					$authService->setStorage($sm->get('Omelettes\Storage\Session'));
				
					return $authService;
				},
				
				// Database migrations
				'OmelettesConsole\Migration\Sql' => function ($sm) {
					$migration = new Migration\Sql();
					$migration->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
					return $migration;
				},
				
				// Email
				'Omelettes\Mailer' => function ($sm) {
					$config = $sm->get('config');
					$defaultAddress = $config['email_addresses']['SYSTEM_NOREPLY'];
					$mailer = new Mailer();
					$mailer->setTextLayout('mail/layout/text')
						->setHtmlLayout('mail/layout/html')
						->setFromAddress($defaultAddress['email'])
						->setFromName($defaultAddress['name']);
					return $mailer;
				},
				
				// Logging
				'Omelettes\Logger' => function ($sm) {
					$config = $sm->get('config');
					$logger = new Logger();
					if (isset($config['log_levels']['stream'])) {
						$streamWriter = new Log\Writer\Stream('php://output');
						$streamfilter = new Log\Filter\Priority($config['log_levels']['stream']);
						$streamWriter->addFilter($streamfilter);
						$logger->addWriter($streamWriter);
					}
					if (isset($config['log_levels']['db'])) {
						$mapping = array(
							'message' => 'message',
							'priority' => 'level',
							'extra' => array('tag' => 'tag'),
						);
						$dbWriter = new Log\Writer\Db($sm->get('Zend\Db\Adapter\Adapter'), 'log', $mapping);
						$dbFilter = new Log\Filter\Priority($config['log_levels']['db']);
						$dbWriter->addFilter($dbFilter);
						$logger->addWriter($dbWriter);
					}
					return $logger;
				},
				
				// Sessions
				'SessionsTableGateway'			=> function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					return new TableGateway('sessions', $dbAdapter);
				},
				'Omelettes\Session\SaveHandler\DbTableGateway'	=> function ($sm) {
					$config = $sm->get('config');
					if (isset($config['session'])) {
						$session = $config['session'];
				
						$tableGateway = $sm->get('SessionsTableGateway');
						$sessionSaveHandler = new SessionSaveHandlerDb($tableGateway, new Session\SaveHandler\DbTableGatewayOptions());
					} else {
						throw new \Exception('Missing session config');
					}
					return $sessionSaveHandler;
				},
				'Zend\Session\SessionManager'	=> function ($sm) {
					$config = $sm->get('config');
					if (isset($config['session'])) {
						$session = $config['session'];
						
						$sessionConfig = null;
						if (isset($session['config'])) {
							$class = isset($session['config']['class']) ?
								$session['config']['class'] :
								'Zend\Session\Config\SessionConfig';
							$options = isset($session['config']['options']) ?
								$session['config']['options'] :
								array();
							$sessionConfig = new $class();
							$sessionConfig->setOptions($options);
						}
						
						$sessionStorage = null;
						if (isset($session['storage'])) {
							$class = $session['storage'];
							$sessionStorage = new $class();
						}
						
						$sessionSaveHandler = null;
						if (isset($session['save_handler'])) {
							// class should be fetched from service manager since it will require constructor arguments
							$sessionSaveHandler = $sm->get($session['save_handler']);
						}
						
						$sessionManager = new Session\SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);
						
						if (isset($session['validator'])) {
							$chain = $sessionManager->getValidatorChain();
							foreach ($session['validator'] as $validator) {
								$validator = new $validator();
								$chain->attach('session.validate', array($validator, 'isValid'));
						
							}
						}
					} else {
						$sessionManager = new Session\SessionManager();
					}
					Session\Container::setDefaultManager($sessionManager);
					return $sessionManager;
				},
				
				// Accounts, Users, Logins and Passwords
				'AccountPlansViewGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\AccountPlan());
					return new TableGateway('account_plans_view', $dbAdapter, null, $resultSetPrototype);
				},
				'Omelettes\Model\AccountPlansMapper' => function($sm) {
					$readGateway = $sm->get('AccountPlansViewGateway');
					$mapper = new Model\AccountPlansMapper($readGateway);
					return $mapper;
				},
				'AccountsTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\Account());
					return new TableGateway('accounts', $dbAdapter, null, $resultSetPrototype);
				},
				'AccountsViewGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\Account());
					return new TableGateway('accounts_view', $dbAdapter, null, $resultSetPrototype);
				},
				'Omelettes\Model\AccountsMapper' => function($sm) {
					$readGateway = $sm->get('AccountsViewGateway');
					$writeGateway = $sm->get('AccountsTableGateway');
					$mapper = new Model\AccountsMapper($readGateway, $writeGateway);
					return $mapper;
				},
				'UserLoginsTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					return new TableGateway('user_logins', $dbAdapter, null, $resultSetPrototype);
				},
				'UsersTableGateway' => function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\AuthUser());
					return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
				},
				'UsersViewGateway' => function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\AuthUser());
					return new TableGateway('users_view', $dbAdapter, null, $resultSetPrototype);
				},
				'Omelettes\Model\AuthUserLoginsMapper' => function($sm) {
					$gateway = $sm->get('UserLoginsTableGateway');
					$mapper = new Model\AuthUserLoginsMapper($gateway, $gateway);
					return $mapper;
				},
				'Omelettes\Model\AuthUsersMapper' => function($sm) {
					$readGateway = $sm->get('UsersViewGateway');
					$writeGateway = $sm->get('UsersTableGateway');
					$mapper = new Model\AuthUsersMapper($readGateway, $writeGateway);
					return $mapper;
				},
				'Omelettes\Form\LoginFilter' => function($sm) {
					$filter = new Form\LoginFilter();
					return $filter;
				},
				'Omelettes\Form\ForgotPasswordFilter' => function($sm) {
					$filter = new Form\ForgotPasswordFilter(
						$sm->get('Omelettes\Model\AuthUsersMapper')
					);
					return $filter;
				},
				'Omelettes\Form\SignupFilter' => function ($sm) {
					$filter = new Form\SignupFilter(
						$sm->get('Omelettes\Model\AuthUsersMapper')
					);
					return $filter;
				},
			),
		);
	}
	
	public function onBootstrap(MvcEvent $ev)
	{
		$this->checkApplicationStatus($ev);
		$this->bootstrapSession($ev);
		
		$app = $ev->getParam('application');
		$eventManager = $app->getEventManager();
		$eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'setLayout'));
		$eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAuth'));
		$eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkAcl'));
	}
	
	public function bootstrapSession(MvcEvent $ev)
	{
		if ($ev->getRequest() instanceof ConsoleRequest) {
			// Use PHP default session handling for console requests
			return;
		}
		
		$session = $ev->getApplication()->getServiceManager()->get('Zend\Session\SessionManager');
		$session->start();
		
		$container = new Session\Container('initialized');
		if (!isset($container->init)) {
			$session->regenerateId(true);
			$container->init = 1;
		}
	}
	
	/**
	 * Checks application has access to database, etc.
	 * 
	 * @param MvcEvent $ev
	 */
	public function checkApplicationStatus(MvcEvent $ev)
	{
		$dbAdapter = $ev->getApplication()->getServiceManager()->get('Zend\Db\Adapter\Adapter');
		if (!$dbAdapter->getDriver()->getConnection()->isConnected()) {
			// No database connection! 
			//throw new \Exception('No database connection');
		}
	}
	
	public function setLayout(MvcEvent $ev)
	{
		$config = $ev->getApplication()->getServiceManager()->get('config');
		if (!isset($config['layout'])) {
			return;
		}
		$layoutConfig = $config['layout'];
		
		$routeName = $ev->getRouteMatch()->getMatchedRouteName();
		if (isset($layoutConfig[$routeName])) {
			$viewModel = $ev->getViewModel();
			$viewModel->setTemplate($layoutConfig[$routeName]);
		}
	}
	
	/**
	 * Ensures that the auth identity is kept fresh
	 * Handles cookie-based authentication
	 *
	 * @param MvcEvent $e
	 */
	public function checkAuth(MvcEvent $ev)
	{
		$app = $ev->getApplication();
		$sm = $app->getServiceManager();
		$config = $sm->get('config');
		$flash = $sm->get('ControllerPluginManager')->get('flashMessenger');
		$auth = $sm->get('AuthService');
		$authMapper = $sm->get('Omelettes\Model\AuthUsersMapper');
	
		$request = $ev->getRequest();
		if ($request instanceof ConsoleRequest) {
			// We're using the console
			// Log in as system user
			$systemIdentity = $authMapper->getSystemIdentity($config['user_keys']['SYSTEM_CONSOLE']);
			if (!$systemIdentity) {
				throw new \Exception('Missing console identity!');
			}
			$auth->getStorage()->write($systemIdentity);
			return;
		}
	
		// HTTP requests might provide a cookie
		$cookie = $request->getCookie();
	
		if ($auth->hasIdentity()) {
			// User is logged in, session is fresh
			$currentIdentity = $auth->getIdentity();
			if (false === ($freshIdentity = $authMapper->find($currentIdentity->key))) {
				// Can't find the user for some reason
				// Maybe they got deleted, so log them out
				$auth->clearIdentity();
				$flash->addErrorMessage('Your authentication idenitity was not found');
				return $this->redirectToRoute($ev, 'login');
			}
			// Refresh the identity
			if ($currentIdentity->isPasswordAuthenticated()) {
				$freshIdentity->setPasswordAuthenticated();
			}
			$auth->getStorage()->write($freshIdentity);
				
		} elseif ($cookie && $cookie->offsetExists('login')) {
			// No auth identity in the current session, but we have a login lookie
			// Attempt a cookie-based authentication
			$userLoginsMapper = $sm->get('Omelettes\Model\AuthUserLoginsMapper');
			try {
				if (FALSE !== ($refreshedCookieData = $userLoginsMapper->verifyCookie($cookie->login))) {
					// Authenticated via cookie data
					$data = $userLoginsMapper->splitCookieData($refreshedCookieData);
						
					// Refresh the cookie
					$auth->setLoginCookie($ev->getResponse(), $refreshedCookieData, $data['expiry']);
						
					// Fetch authentication identity
					if (FALSE !== ($user = $authMapper->findByName($data['name']))) {
						// Authenticated identity IS NOT password authenticated!
						$auth->getStorage()->write($user);
					} else {
						$auth->removeLoginCookie($ev->getResponse());
						throw new \Exception('Failed to authenticate using cookie data');
					}
				} else {
					$auth->removeLoginCookie($ev->getResponse());
					return;
				}
			} catch (Exception\UserLoginTheftException $e) {
				// The provided login cookie has a known series but an unknown token; possible cookie theft
				// Attempt to remove the cookie
				$auth->removeLoginCookie($ev->getResponse());
				// Give the user some warning
				return $this->redirectToRoute($ev, 'login-theft-warning');
			}
		} else {
			// No identity in session or cookie; user is not logged in
		}
	}
	
	/**
	 * Performs an ACL check, using the user's current role ('guest' if not logged in)
	 * against the current resource (the route name). Configured via the 'acl' key in
	 * the module config.
	 *
	 * @param MvcEvent $e
	 * @throws \Exception
	 */
	public function checkAcl(MvcEvent $ev)
	{
		$app = $ev->getApplication();
		$sm = $app->getServiceManager();
		$acl = $sm->get('AclService');
		$auth = $sm->get('AuthService');
		$flash = $sm->get('ControllerPluginManager')->get('flashMessenger');
	
		$resource = $ev->getRouteMatch()->getMatchedRouteName();
		if ($resource === 'login') {
			// Skip the check if we are attempting to access the login page
			return;
		}
		$privilege = $ev->getRouteMatch()->getParam('action', 'index');
	
		$role = 'guest';
		if ($auth->hasIdentity()) {
			$role = $auth->getIdentity()->aclRole;
		}
		if (!$acl->hasResource($resource)) {
			throw new \Exception('Undefined ACL resource: ' . $resource);
		}
		if (!$acl->isAllowed($role, $resource, $privilege)) {
			// ACL role is not allowed to access this resource/privilege
			if ('guest' === $role) {
				// User is not logged in
				$flash->addErrorMessage('You must be logged in to access that page');
				return $this->redirectToRoute($ev, 'login');
			} else {
				// User is logged in, probably tried to access an admin-only resource/privilege
				$flash->addErrorMessage('You do not have permission to access that page');
				return $this->redirectToRoute($ev, 'home');
			}
		}
	}
	
	protected function redirectToRoute(MvcEvent $ev, $routeName = 'login')
	{
		// Redirect to login page
		$loginUrl = $ev->getRouter()->assemble(array(), array('name' => $routeName));
		$response = $ev->getResponse();
		$response->getHeaders()->addHeaderLine('Location', $ev->getRequest()->getBaseUrl() . $loginUrl);
		$response->setStatusCode('302');
			
		// Return a response to short-circuit the event manger and prevent a dispatch
		return $response;
	}
	
	public function getConsoleBanner(Console $console)
	{
		return
			"==------------------------------------------------------==\n" .
			"==        OMELETT.ES APPLICATION CONSOLE                ==\n" .
			"==------------------------------------------------------=="
		;
	}
	
	public function getConsoleUsage(Console $console)
	{
		return array(
			'db migrate [--commit] [--all]'		=> 'Execute (and --commit) next database migration (or --all of them)',
			'build <assets>'					=> '<assets> must be one of: css',
		);
	}
	
}
