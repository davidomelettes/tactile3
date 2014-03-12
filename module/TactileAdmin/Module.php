<?php

namespace TactileAdmin;

use Zend\Db\ResultSet\ResultSet,
	Zend\Db\TableGateway\TableGateway;

class Module
{
	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}
	
	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}
	
	public function getServiceConfig()
	{
		return array(
			'factories' => array(
				// Resources
				'ResourcesViewGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\Resource());
					return new TableGateway('resources_view', $dbAdapter, null, $resultSetPrototype);
				},
				'ResourcesTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\Resource());
					return new TableGateway('resources', $dbAdapter, null, $resultSetPrototype);
				},
				'TactileAdmin\Model\ResourcesMapper' => function($sm) {
					$readGateway = $sm->get('ResourcesViewGateway');
					$writeGateway = $sm->get('ResourcesTableGateway');
					$mapper = new Model\ResourcesMapper($readGateway, $writeGateway);
					return $mapper;
				},
				
				// Users
				'TactileAdmin\Form\AddUserFilter' => function ($sm) {
					$filter = new Form\AddUserFilter($sm->get('Omelettes\Model\AuthUsersMapper'));
					return $filter;
				},
			),
		);
	}
	
}
