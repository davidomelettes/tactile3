<?php

namespace Omelettes\Controller;

use Omelettes\Model;

trait AuthUsersTrait
{
	/**
	 * @var Model\AuthUsersMapper
	 */
	protected $usersMapper;
	
	/**
	 * @var Model\AuthUserLoginsMapper
	 */
	protected $userLoginsMapper;
	
	public function getUsersMapper()
	{
		if (!$this->usersMapper) {
			$usersMapper = $this->getServiceLocator()->get('Omelettes\Model\AuthUsersMapper');
			$this->usersMapper = $usersMapper;
		}
	
		return $this->usersMapper;
	}
	
	public function getUserLoginsMapper()
	{
		if (!$this->userLoginsMapper) {
			$userLoginsMapper = $this->getServiceLocator()->get('Omelettes\Model\AuthUserLoginsMapper');
			$this->userLoginsMapper = $userLoginsMapper;
		}
	
		return $this->userLoginsMapper;
	}
	
}
