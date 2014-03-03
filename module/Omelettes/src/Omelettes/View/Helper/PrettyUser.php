<?php

namespace Omelettes\View\Helper;

use OmelettesAuth\Model\UsersMapper;

class PrettyUser extends AbstractPrettifier
{
	/**
	 * @var UsersMapper
	 */
	protected $usersMapper;
	
	public function getUsersMapper()
	{
		if (!$this->usersMapper) {
			$mapper = $this->getApplicationServiceLocator()->get('OmelettesAuth\Model\UsersMapper');
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
