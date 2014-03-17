<?php

namespace Omelettes\Controller;

use Omelettes\Model;
use Zend\Navigation;

trait CrudNavigationTrait
{
	public function constructNavigation($config)
	{
		$factory = new Navigation\Service\ConstructedNavigationFactory($config);
		$navigation = $factory->createService($this->getServiceLocator());
	
		return $navigation;
	}
	
	abstract public function getIndexNavigationConfig();
	
	abstract public function getViewNavigationConfig(Model\NamedItemModel $model);
	
}
