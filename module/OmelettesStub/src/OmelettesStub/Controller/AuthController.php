<?php

namespace OmelettesStub\Controller;

use Omelettes\Controller\AuthController as OmAuthController;

class AuthController extends OmAuthController
{
	public function frontAction()
	{
		return "hello!";
	}
	
}
