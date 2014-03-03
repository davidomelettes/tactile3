<?php

namespace Omelettes\Validator;

use Zend\Authentication\AuthenticationService,
	Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\Validator\AbstractValidator,
	Zend\Validator\Exception;

class Password extends AbstractValidator 
{
	const NO_IDENTITY = 'noIdentity';
	const NOT_MATCH = 'regexNotMatch';
	
	protected $messageTemplates = array(
		self::NOT_MATCH => "Entered value does not match your current password",
		self::NO_IDENTITY => "You are not logged in",
	);
	
	/**
	 * @var AuthenticationService
	 */
	protected $authService;
	
	public function __construct($options = null)
	{
		parent::__construct($options);
	
		if ($options instanceof AuthenticationService) {
			$this->setAuthService($options);
			return;
		}
	
		if (!array_key_exists('authService', $options)) {
			throw new Exception\InvalidArgumentException('AuthService option missing!');
		}
		$this->setAuthService($options['authService']);
	}
	
	public function setAuthService(AuthenticationService $authService)
	{
		$this->authService = $authService;
		
		return $this;
	}
	
	public function isValid($value)
	{
		$this->setValue($value);
		$valid = true;
		
		// Cannot validate unless we have an auth identity
		if (!$this->authService->hasIdentity()) {
			$valid = false;
			$this->error(self::NO_IDENTITY);
		} else {
			$this->authService->getAdapter()
				->setIdentity($this->authService->getIdentity()->name)
				->setCredential($value);
			// Call authenticate() from the adapter so as not to overwrite the current identity
			$result = $this->authService->getAdapter()->authenticate();
			if (!$result->isValid()) {
				$valid = false;
				$this->error(self::NOT_MATCH);
			}
		}
		
		return $valid;
	}
	
}
