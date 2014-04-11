<?php

namespace Omelettes\View\Helper;

use Omelettes\Model;

class PrettyUser extends AbstractPrettifier
{
	/**
	 * @var Model\AuthUsersMapper
	 */
	protected $usersMapper;
	
	public function getUsersMapper()
	{
		if (!$this->usersMapper) {
			$mapper = $this->getApplicationServiceLocator()->get('Omelettes\Model\AuthUsersMapper');
			$this->usersMapper = $mapper;
		}
		
		return $this->usersMapper;
	}
	
	public function __invoke($userKey, $when = null)
	{
		$partialHelper = $this->view->plugin('partial');
		return $partialHelper('pretty/user', array('user' => $this->getUsersMapper()->find($userKey), 'when' => $when));
	}

}
