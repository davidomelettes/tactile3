<?php

namespace Omelettes\Storage;

use Zend\Authentication\Storage\Session as ZendSessionStorage;

class Session extends ZendSessionStorage
{
	const STORAGE_NAMESPACE = 'Omelettes';
	
	public function getSessionManager()
	{
		return $this->session->getManager();
	}
	
	public function rememberMe()
	{
		$this->session->getManager()->rememberMe();
	}
	
	public function forgetMe()
	{
		$this->session->getManager()->forgetMe();
	}
	
}
