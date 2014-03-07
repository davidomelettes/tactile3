<?php

namespace Omelettes\Model;

use Zend\Permissions\Acl\Acl;

class AuthUser extends AccountBoundQuantumModel
{
	protected $propertyMap = array(
		'fullName'	=> 'full_name',
		'aclRole'	=> 'acl_role',
	);
	
	protected $fullName;
	protected $aclRole;
	
	protected $passwordAuthenticated = false;
	
	public function setPasswordAuthenticated($authenticated = true)
	{
		$this->passwordAuthenticated = (boolean)$authenticated;
	}
	
	public function isPasswordAuthenticated()
	{
		return $this->passwordAuthenticated;
	}
	
}
