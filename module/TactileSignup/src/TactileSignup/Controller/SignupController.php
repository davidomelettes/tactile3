<?php

namespace TactileSignup\Controller;

use Omelettes\Controller;
use TactileAdmin\Controller\ResourcesTrait;

class SignupController extends Controller\SignupController
{
	use ResourcesTrait;
	
	protected function postSignupSetup()
	{
		// Insert default resources
		$this->getResourcesMapper()->installSystemResources();
	}
	
}
