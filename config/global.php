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

use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

return array(
    
    
    
    'service_manager' => array(
        
        'factories' => array(
            
            'Zend\Db\Adapter\Adapter' => function ($sm) {
                return $adapter = new Adapter(array(
                    'driver' => 'Pdo_Mysql',
                    'hostname' => 'localhost',
                    'database' => 'mla',
                    'username' => 'root',
                    'password' => '',
                    'charset' => 'utf-8',
                    'driver_options' => array(
                        \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
                    )
                
                ));
            },
            
            // Authentication Service
            'AuthService' => function ($sm) {
                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'mla_users', 'email', 'password', 'MD5(?)');
                $authService = new AuthenticationService();
                $authService->setAdapter($dbTableAuthAdapter);
                return $authService;
            },
            
            // Email Service
            'SmtpTransportService' => function ($sm) {
                
                $transport = new SmtpTransport();
                $options = new SmtpOptions(array(
                    'name' => 'Web.de',
                    'host' => 'smtp.web.de',
                    'port' => '587',
                    'connection_class' => 'login',
                    'connection_config' => array(
                        'username' => 'mib-team@web.de',
                        'password' => 'mib2009',
                        'ssl' => 'tls'
                    )
                ));
                
                $transport->setOptions($options);
                return $transport;
            },
            
            // Email Service
            'mla-web@outlook.com' => function ($sm) {
                
                $transport = new SmtpTransport();
                $options = new SmtpOptions(array(
                    'name' => 'Outlook.com',
                    'host' => 'smtp-mail.outlook.com',
                    'port' => '587',
                    'connection_class' => 'login',
                    'connection_config' => array(
                        'username' => 'mla-web@outlook.com',
										'password' => 'MLA#2016',
										'ssl' => 'tls'
								)
						) );
							
							
						$transport->setOptions ( $options );
						return $transport;
						}
				) 
		),
);
