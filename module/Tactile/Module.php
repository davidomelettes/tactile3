<?php

namespace Tactile;

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
				// Resources & Quanta
				'ResourcesViewGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\Resource());
					return new TableGateway('resources_view', $dbAdapter, null, $resultSetPrototype);
				},
				'Tactile\Model\ResourcesMapper' => function($sm) {
					$readGateway = $sm->get('ResourcesViewGateway');
					$mapper = new Model\ResourcesMapper($readGateway);
					return $mapper;
				},
				'Tactile\Model\ResourceMapper' => function($sm) {
					$readGateway = $sm->get('AdminResourcesViewGateway');
					$mapper = new Model\ResourcesMapper($readGateway);
					return $mapper;
				},
				'QuantaTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\Contact());
					return new TableGateway('quanta', $dbAdapter, null, $resultSetPrototype);
				},
				'QuantaViewGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\Contact());
					return new TableGateway('quanta_view', $dbAdapter, null, $resultSetPrototype);
				},
				
				// Contacts
				'Tactile\Model\ContactsMapper' => function ($sm) {
					$readGateway = $sm->get('QuantaViewGateway');
					$writeGateway = $sm->get('QuantaTableGateway');
					$mapper = new Model\ContactsMapper($readGateway, $writeGateway);
					$resourcesMapper = $sm->get('Tactile\Model\ResourcesMapper');
					$mapper->setResource($resourcesMapper->findByName('contacts'));
					return $mapper;
				},
				'Tactile\Form\ContactFilter' => function ($sm) {
					$filter = new Form\ContactFilter();
					return $filter;
				},
			),
		);
	}
	
}
