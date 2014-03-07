<?php

// TactileSignup
return array(
	'acl' => array(
		'resources' => array(
			'guest'		=> array(
				'front' => array(),
				'plans' => array(),
				'signup' => array(),
			),
		),
	),
	'controllers' => array(
		'invokables' => array(
			'Signup\Controller\Pages' => 'TactileSignup\Controller\PagesController',
			'Signup\Controller\Signup' => 'TactileSignup\Controller\SignupController',
		),
	),
	'layout' => array(
		'front' => 'layout/front',
		'plans' => 'layout/front',
		'signup' => 'layout/signup',
	),
	'navigation' => array(
		'default' => array(
		),
	),
	'router' => array(
		'routes' => array(
			'front' => array(
				'type'		=> 'Literal',
				'options'	=> array(
					'route'			=> '/',
					'defaults'		=> array(
						'controller'	=> 'Signup\Controller\Pages',
						'action'		=> 'front',
					),
				),
			),
			'plans' => array(
				'type'		=> 'Literal',
				'options'	=> array(
					'route'			=> '/plans',
					'defaults'		=> array(
						'controller'	=> 'Signup\Controller\Pages',
						'action'		=> 'front',
					),
				),
			),
			'signup' => array(
				'type'		=> 'Literal',
				'options'	=> array(
					'route'			=> '/sign-up',
					'defaults'		=> array(
						'controller'	=> 'Signup\Controller\Signup',
						'action'		=> 'signup',
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
		),
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),
);
