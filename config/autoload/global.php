<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'db' => array(
		'driver'			=> 'Pdo',
		'dsn'				=> 'pgsql:host=localhost;port=5433;dbname=omelettes',
	),
	'service_manager' => array(
		'factories' => array(
			'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
		),
	),
	'session'			=> array(
		'config'			=> array(
			'class'				=> 'Zend\Session\Config\SessionConfig',
			'options'			=> array(
				'name'				=> 'Omelettes',
				'use_cookies'		=> true,
				'cookie_httponly'	=> true,
			),
		),
		'storage'			=> 'Zend\Session\Storage\SessionArrayStorage',
		'save_handler'		=> 'Omelettes\Session\SaveHandler\DbTableGateway',
		'validators'		=> array(
			'Zend\Session\Validator\RemoteAddr',
			'Zend\Session\Validator\HttpUserAgent',
		),
	),
	'user_keys'			=> array(
		'SYSTEM_SYSTEM'		=> 'deadbeef7a6940e789848d3de3bedc0b',
		'SYSTEM_CONSOLE'	=> 'bedabb1e66ff47f0a3f01f3f45b5c94d',
		'SYSTEM_SIGNUP'		=> 'feedfacead3e4cc6bd9c501224e24359',
	),
	'email_addresses'	=> array(
		'SYSTEM_NOREPLY' => array(
			'email'			=> 'noreply@localhost',
			'name'			=> 'Omelettes',
		),
	),
	'view_manager' => array(
		'base_path'			=> 'http://localhost:8888',
	),
	'paths' => array(
		'resource-fixture' => __DIR__ . '/../../fixtures/resources.xml',
	),
);
