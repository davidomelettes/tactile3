<?php

// OmelettesStub
return array(
	'acl' => array(
		'resources' => array(
			'guest'		=> array(
				'front' => array(),
				'home' => array(),
				'signup' => array(),
				'login' => array(),
				'logout' => array(),
				'forgot-password' => array(),
				'reset-password' => array(),
				'login-theft-warning' => array(),
			),
		),
	),
	'controllers' => array(
		'invokables' => array(
			'Stub\Controller\Auth' => 'OmelettesStub\Controller\AuthController',
			'Stub\Controller\Signup' => 'OmelettesStub\Controller\SignupController',
			'Stub\Controller\Pages' => 'OmelettesStub\Controller\PagesController',
		),
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
						'controller'	=> 'Stub\Controller\Pages',
						'action'		=> 'front',
					),
				),
			),
			'home' => array(
				'type'		=> 'Literal',
				'options'	=> array(
					'route'			=> '/',
					'defaults'		=> array(
						'controller'	=> 'Stub\Controller\Pages',
						'action'		=> 'front',
					),
				),
			),
			'signup' => array(
				'type'		=> 'Literal',
				'options'	=> array(
					'route'			=> '/sign-up',
					'defaults'		=> array(
						'controller'	=> 'Stub\Controller\Signup',
						'action'		=> 'signup',
					),
				),
			),
			'login' => array(
				'type'		=> 'Literal',
				'options'	=> array(
					'route'			=> '/sign-in',
					'defaults'		=> array(
						'controller'	=> 'Stub\Controller\Auth',
						'action'		=> 'login',
					),
				),
			),
			'logout' => array(
				'type'		=> 'Literal',
				'options'	=> array(
					'route'			=> '/sign-out',
					'defaults'		=> array(
						'controller'	=> 'Stub\Controller\Auth',
						'action'		=> 'logout',
					),
				),
			),
			'forgot-password' => array(
				'type'		=> 'Literal',
				'options'	=> array(
					'route'			=> '/forgot-password',
					'defaults'		=> array(
						'controller'	=> 'Stub\Controller\Auth',
						'action'		=> 'forgot-password',
					),
				),
			),
			'reset-password' => array(
				'type'		=> 'Segment',
				'options'	=> array(
					'route'			=> '/reset-password/:user_key/:password_reset_key',
					'defaults'		=> array(
						'controller'	=> 'Stub\Controller\Auth',
						'action'		=> 'reset-password',
					),
					'constraints'	=> array(
						'user_key'				=> Omelettes\Validator\Uuid::UUID_REGEX_PATTERN,
						'passsword_reset_key'	=> Omelettes\Validator\Uuid::UUID_REGEX_PATTERN,
					),
				),
			),
			'login-theft-warning' => array(
				'type'		=> 'Literal',
				'options'	=> array(
					'route'			=> '/login-theft-warning',
					'defaults'		=> array(
						'controller'	=> 'Stub\Controller\Auth',
						'action'		=> 'login-theft-warning',
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
			'mail/text/reset-password'	=> __DIR__ . '/../view/mail/reset-password.phtml',
			'mail/html/reset-password'	=> __DIR__ . '/../view/mail/html/reset-password.phtml',
		),
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),
);
