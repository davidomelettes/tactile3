<?php

// Tactile
return array(
	'acl' => array(
		'resources' => array(
			'user' => array(
				'home' => array(),
				'contacts' => array(),
				'opportunities' => array(),
				'activities' => array(),
				'comms' => array(),
				'search' => array(),
			),
		),
	),
	'controllers' => array(
		'invokables' => array(
			'Tactile\Controller\Home' => 'Tactile\Controller\HomeController',
			'Tactile\Controller\Contacts' => 'Tactile\Controller\ContactsController',
			'Tactile\Controller\Opportunities' => 'Tactile\Controller\OpportunitiesController',
			'Tactile\Controller\Activities' => 'Tactile\Controller\ActivitiesController',
			'Tactile\Controller\Communication' => 'Tactile\Controller\CommunicationController',
			'Tactile\Controller\Search' => 'Tactile\Controller\SearchController',
		),
	),
	'navigation' => array(
		'default' => array(
			array(
				'label' => 'Dashboard',
				'route' => 'home',
			),
			array(
				'label' => 'Contacts',
				'route' => 'contacts',
			),
			array(
				'label' => 'Opportunities',
				'route' => 'opportunities',
			),
			array(
				'label' => 'Activities',
				'route' => 'activities',
			),
			array(
				'label' => 'Communication',
				'route' => 'comms',
			),
		),
	),
	'router' => array(
		'routes' => array(
			'home' => array(
				'type' => 'Segment',
				'options' => array(
					'route'			=> '/dash',
					'defaults'		=> array(
						'controller'	=> 'Tactile\Controller\Home',
						'action'		=> 'home',
					),
				),
			),
			'contacts' => array(
				'type' => 'Segment',
				'options' => array(
					'route'			=> '/contacts[/:action][/:key]',
					'constraints'	=> array(
						'key'			=> Omelettes\Validator\Uuid::UUID_REGEX_PATTERN,
					),
					'defaults'		=> array(
						'controller'	=> 'Tactile\Controller\Contacts',
						'action'		=> 'index',
					),
				),
			),
			'opportunities' => array(
				'type' => 'Segment',
				'options' => array(
					'route'			=> '/opportunities',
					'defaults'		=> array(
						'controller'	=> 'Tactile\Controller\Opportunities',
						'action'		=> 'index',
					),
				),
			),
			'activities' => array(
				'type' => 'Segment',
				'options' => array(
					'route'			=> '/activities',
					'defaults'		=> array(
						'controller'	=> 'Tactile\Controller\Activities',
						'action'		=> 'index',
					),
				),
			),
			'comms' => array(
				'type' => 'Segment',
				'options' => array(
					'route'			=> '/comms',
					'defaults'		=> array(
						'controller'	=> 'Tactile\Controller\Communication',
						'action'		=> 'index',
					),
				),
			),
			'search' => array(
			'type' => 'Segment',
			'options' => array(
				'route'			=> '/search',
				'defaults'		=> array(
					'controller'	=> 'Tactile\Controller\Search',
					'action'		=> 'index',
				),
			),
			),
		),
	),
	'service_manager' => array(
	),
	'view_helpers'	=> array(
		'invokables'	=> array(
		),
	),
	'view_manager' => array(
		'template_map' => array(
			'html-head'					=> __DIR__ . '/../view/partial/html-head.phtml',
			'html-body-end'				=> __DIR__ . '/../view/partial/html-body-end.phtml',
			'navigation-top'			=> __DIR__ . '/../view/partial/navigation-top.phtml',
			'navigation-bottom'			=> __DIR__ . '/../view/partial/navigation-bottom.phtml',
		),
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),
);
