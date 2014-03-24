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
				'AdminResourcesViewGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\Resource());
					return new TableGateway('resources_view', $dbAdapter, null, $resultSetPrototype);
				},
				'AdminResourcesTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\Resource());
					return new TableGateway('resources', $dbAdapter, null, $resultSetPrototype);
				},
				'TactileAdmin\Model\ResourcesMapper' => function($sm) {
					$readGateway = $sm->get('AdminResourcesViewGateway');
					$writeGateway = $sm->get('AdminResourcesTableGateway');
					$mapper = new Model\ResourcesMapper($readGateway, $writeGateway);
					return $mapper;
				},
				'TactileAdmin\Form\ResourceFilter' => function($sm) {
					$filter = new Form\ResourceFilter($sm->get('TactileAdmin\Model\ResourcesMapper'));
					return $filter;
				},
				'TactileAdmin\Form\ResourceMetaFilter' => function($sm) {
					$filter = new Form\ResourceMetaFilter();
					return $filter;
				},
				'AdminResourceFieldsViewGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\ResourceField());
					return new TableGateway('resource_fields_view', $dbAdapter, null, $resultSetPrototype);
				},
				'AdminResourceFieldsTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\ResourceField());
					return new TableGateway('resource_fields', $dbAdapter, null, $resultSetPrototype);
				},
				'TactileAdmin\Model\ResourceFieldsMapper' => function($sm) {
					$readGateway = $sm->get('AdminResourceFieldsViewGateway');
					$writeGateway = $sm->get('AdminResourceFieldsTableGateway');
					$mapper = new Model\ResourceFieldsMapper($readGateway, $writeGateway);
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
