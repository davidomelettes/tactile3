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
				// Contacts
				'ContactsTableGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\Contact());
					return new TableGateway('contacts', $dbAdapter, null, $resultSetPrototype);
				},
				'ContactsViewGateway' => function ($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Model\Contact());
					return new TableGateway('contacts_view', $dbAdapter, null, $resultSetPrototype);
				},
				'Tactile\Model\ContactsMapper' => function ($sm) {
					$readGateway = $sm->get('ContactsViewGateway');
					$writeGateway = $sm->get('ContactsTableGateway');
					$mapper = new Model\ContactsMapper($readGateway, $writeGateway);
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
