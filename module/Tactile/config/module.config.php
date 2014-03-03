<?php

// Tactile
return array(
	'acl' => array(
		'resources' => array(
			'guest'		=> array(
				'home' => array(),
			),
		),
	),
	'controllers' => array(
		'invokables' => array(
			'Tactile\Controller\Home' => 'Tactile\Controller\HomeController',
		),
	),
	'navigation' => array(
		'default' => array(
		),
	),
	'router' => array(
		'routes' => array(
			'home' => array(
				'type' => 'Segment',
				'options' => array(
					'route'			=> '/dash',
					'constraints'	=> array(
						'key'			=> Omelettes\Validator\Uuid::UUID_REGEX_PATTERN,
					),
					'defaults'		=> array(
						'controller'	=> 'Tactile\Controller\Home',
						'action'		=> 'home',
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
