<?php

namespace Omelettes\Controller;

use Omelettes\Model;
use Omelettes\Paginator\Paginator;

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
	
	/**
	 *  @var Paginator
	 */
	protected $usersPaginator;
	
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
	
	/**
	 * @return Paginator
	 */
	public function getUsersPaginator($page = 1)
	{
		if (!$this->usersPaginator) {
			$usersPaginator = $this->getUsersMapper()->fetchAll(true);
			$usersPaginator->setCurrentPageNumber($page);
			$this->usersPaginator = $usersPaginator;
		}
	
		return $this->usersPaginator;
	}
	
}
