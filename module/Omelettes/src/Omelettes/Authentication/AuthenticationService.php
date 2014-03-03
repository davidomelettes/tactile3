<?php

namespace Omelettes\Authentication;

use Zend\Authentication\AuthenticationService as ZendAuthService,
	Zend\Http\Header\SetCookie,
	Zend\Http\Response as HttpResponse;

class AuthenticationService extends ZendAuthService
{
	const REMEMBER_ME_EXPIRY_STRING = '+2 weeks';
	
	/**
	 * Sets the 'remember me' cookie
	 * 
	 * @param HttpResponse $response
	 * @param string $cookieData
	 * @param string $expiry
	 * @return \OmelettesAuth\Authentication\AuthenticationService
	 */
	public function setLoginCookie(HttpResponse $response, $cookieData, $expiry  = null)
	{
		if (null === $expiry) {
			$expiry = date('U', strtotime(self::REMEMBER_ME_EXPIRY_STRING));
		}
		$setCookieHeader = new SetCookie(
			'login',
			(string)$cookieData,
			(int)$expiry,
			'/'
		);
		$response->getHeaders()->addHeader($setCookieHeader);
		
		return $this;
	}
	
	/**
	 * Asks the user agent to expire the 'remember me' cookie
	 * 
	 * @param HttpResponse $response
	 * @return \OmelettesAuth\Authentication\AuthenticationService
	 */
	public function removeLoginCookie(HttpResponse $response)
	{
		$setCookieHeader = new SetCookie(
			'login',
			'',
			(int)date('U', strtotime('-2 weeks')),
			'/'
		);
		$response->getHeaders()->addHeader($setCookieHeader);
		
		return $this;
	}
	
}
