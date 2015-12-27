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

use Zend\Db\Adapter\Adapter;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

return array(

 'service_manager' => array(

       'factories' => array(

       		'Zend\Db\Adapter\Adapter'=> function ($sm){
       			return $adapter = new Adapter(array(
       					'driver'         => 'Pdo_Mysql',
       					'hostname'       => 'localhost',
       					'database'       => 'mla',
       					'username'       => 'root',
       					'password'       =>  '',
       					'charset' 		 => 'utf-8'
       			));
       		},

       		// Authentication Service
       		'AuthService' => function($sm) {
       			$dbAdapter           = $sm->get('Zend\Db\Adapter\Adapter');
       			$dbTableAuthAdapter  = new DbTableAuthAdapter($dbAdapter,
       					'mla_users','email','password', 'MD5(?)');
       			$authService = new AuthenticationService();
       			$authService->setAdapter($dbTableAuthAdapter);
       			return $authService;
       		},
       ),
    ),
);
