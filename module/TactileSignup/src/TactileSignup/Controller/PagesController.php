<?php

namespace TactileSignup\Controller;

use Omelettes\Controller;

class PagesController extends Controller\AbstractController
{
	use Controller\AuthUsersTrait;
	
	public function frontAction()
	{
		return array();
	}
	
}
