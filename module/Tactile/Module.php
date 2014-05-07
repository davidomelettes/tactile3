<?php

namespace Tactile;

use Zend\Db\ResultSet\ResultSet,
	Zend\Db\TableGateway\TableGateway,
	Zend\Mvc\MvcEvent;

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
				'Tactile\Model\Resource' => function ($sm) {
					// Factory Resources here so there are ServiceHandler-aware
					$model = new Model\Resource();
					return $model;
				},
				'ResourcesViewGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype($sm->get('Tactile\Model\Resource'));
					return new TableGateway('resources_view', $dbAdapter, null, $resultSetPrototype);
				},
				'Tactile\Model\ResourcesMapper' => function ($sm) {
					$readGateway = $sm->get('ResourcesViewGateway');
					$mapper = new Model\ResourcesMapper($readGateway);
					return $mapper;
				},
				'ResourceService' => function ($sm) {
					$service = new Service\ResourceService($sm->get('Tactile\Model\ResourcesMapper'));
					return $service;
				},
				
				// Resource Fields
				'Tactile\Model\ResourceField' => function ($sm) {
					$model = new Model\ResourceField();
					return $model;
				},
				'ResourceFieldsViewGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype($sm->get('Tactile\Model\ResourceField'));
					return new TableGateway('resource_fields_view', $dbAdapter, null, $resultSetPrototype);
				},
				'Tactile\Model\ResourceFieldsMapper' => function ($sm) {
					$readGateway = $sm->get('ResourceFieldsViewGateway');
					$mapper = new Model\ResourceFieldsMapper($readGateway);
					return $mapper;
				},
				
				// Locales
				'LocalesViewGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					return new TableGateway('locales_view', $dbAdapter, null, $resultSetPrototype);
				},
				'Tactile\Model\LocalesMapper' => function ($sm) {
					$readGateway = $sm->get('LocalesViewGateway');
					$mapper = new Model\LocalesMapper($readGateway);
					return $mapper;
				},
				
				// Quanta
				'Tactile\Model\Quantum' => function ($sm) {
					$model = new Model\Quantum();
					return $model;
				},
				'QuantaTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					return new TableGateway('quanta', $dbAdapter);
				},
				'QuantaViewGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype($sm->get('Tactile\Model\Quantum'));
					return new TableGateway('quanta_view', $dbAdapter, null, $resultSetPrototype);
				},
				'Tactile\Model\QuantaMapper' => function ($sm) {
					$readGateway = $sm->get('QuantaViewGateway');
					$writeGateway = $sm->get('QuantaTableGateway');
					$mapper = new Model\QuantaMapper($readGateway, $writeGateway);
					return $mapper;
				},
				'Tactile\Form\QuantumFilter' => function ($sm) {
					$filter = new Form\QuantumFilter();
					return $filter;
				},
				
				
				// Contacts
				'Tactile\Model\Contact' => function ($sm) {
					$model = new Model\Contact();
					return $model;
				},
				'ContactsTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					return new TableGateway('quanta', $dbAdapter);
				},
				'ContactsViewGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype($sm->get('Tactile\Model\Contact'));
					return new TableGateway('quanta_view', $dbAdapter, null, $resultSetPrototype);
				},
				'Tactile\Model\ContactsMapper' => function ($sm) {
					$readGateway = $sm->get('ContactsViewGateway');
					$writeGateway = $sm->get('ContactsTableGateway');
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
