<?php

namespace Omelettes\Service;

use Omelettes\Model;
use Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorAwareTrait;

class AuthUsersService implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;
	
	/**
	 * @var Model\AuthUsersMapper
	 */
	protected $usersMapper;
	
	protected $users = array();
	
	public function __construct(Model\AuthUsersMapper $mapper)
	{
		$this->usersMapper = $mapper;
		$this->loadUsers();
	}
	
	public function loadUsers()
	{
		$users = $this->usersMapper->fetchAll();
		$this->users = $users;
		
		return $this;
	}
	
	public function getUsers()
	{
		return $this->users;
	}
	
}