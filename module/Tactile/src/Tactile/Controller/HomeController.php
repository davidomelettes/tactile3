<?php

namespace Tactile\Controller;

use Omelettes\Controller;

class HomeController extends Controller\AbstractController
{
	protected function postDispatch()
	{
		$viewHelperManager = $this->getServiceLocator()->get('viewHelperManager');
		$headTitleHelper = $viewHelperManager->get('headTitle');
		$headTitleHelper->append('Dashboard');
	
		return;
	}
	
	public function homeAction()
	{
		
	}
	
	public function welcomeAction()
	{
		
	}
	
}
