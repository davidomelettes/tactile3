<?php

// TactileAdmin
return array(
	'acl' => array(
		'resources' => array(
			'admin' => array(
				'admin' => array(),
				'admin/account' => array(),
				'admin/resources' => array(),
				'admin/users' => array(),
			),
		),
	),
	'controllers' => array(
		'invokables' => array(
			'TactileAdmin\Controller\Account' => 'TactileAdmin\Controller\AccountController',
			'TactileAdmin\Controller\Resources' => 'TactileAdmin\Controller\ResourcesController',
			'TactileAdmin\Controller\Users' => 'TactileAdmin\Controller\UsersController',
		),
	),
	'navigation' => array(
		'default' => array(
		),
	),
	'router' => array(
		'routes' => array(
			'admin' => array(
				'type' => 'Segment',
				'options' => array(
					'route'			=> '/admin',
					'defaults'		=> array(
						'controller'	=> 'TactileAdmin\Controller\Account',
						'action'		=> 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'account' => array(
						'type' => 'Segment',
						'options' => array(
							'route'			=> '/account[/:action]',
							'defaults'		=> array(
								'controller'	=> 'TactileAdmin\Controller\Account',
								'action'		=> 'index',
							),
						),
					),
					'users' => array(
						'type' => 'Segment',
						'options' => array(
							'route'			=> '/users[/:action][/:key]',
							'constraints'	=> array(
								'key'			=> Omelettes\Validator\Uuid::UUID_REGEX_PATTERN,
							),
							'defaults'		=> array(
								'controller'	=> 'TactileAdmin\Controller\Users',
								'action'		=> 'index',
							),
						),
					),
					'resources' => array(
						'type' => 'Segment',
						'options' => array(
							'route'			=> '/resources[/:action][/:resource_name]',
							'defaults'		=> array(
								'controller'	=> 'TactileAdmin\Controller\Resources',
								'action'		=> 'index',
							),
						),
					),
				),
			),
		),
	),
	'service_manager' => array(
	),
	'validators' => array(
		'invokables' => array(
			'NotRoute' => 'TactileAdmin\Validator\NotRoute',
		),
	),
	'view_helpers'	=> array(
		'invokables'	=> array(
		),
	),
	'view_manager' => array(
		'template_map' => array(
			'mail/text/add-user'		=> __DIR__ . '/../view/mail/add-user.phtml',
			'mail/html/add-user'		=> __DIR__ . '/../view/mail/html/add-user.phtml',
			'tabulate/resource'			=> __DIR__ . '/../view/partial/tabulate/resource.phtml',
		),
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),
);
