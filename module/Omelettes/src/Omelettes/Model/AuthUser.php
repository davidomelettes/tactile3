<?php

namespace Omelettes\Model;

use Zend\Permissions\Acl\Acl;

class AuthUser extends QuantumModel
{
	protected $propertyMap = array(
		'fullName'	=> 'full_name',
		'aclRole'	=> 'acl_role',
		'locale'	=> 'locale',
	);
	
	protected $fullName;
	protected $aclRole;
	protected $locale;
	
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
