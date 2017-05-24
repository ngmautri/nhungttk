<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array (
		
		// Testing
		'doctrine' => array (
				'connection' => array (
						'orm_default' => array (
								'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
								'params' => array (
										'host' => 'localhost',
										'port' => '3306',
										'user' => 'root',
										'password' => 'kflg79',
										'dbname' => 'mla'
								),
								// To automatically convert enum to string
								'doctrine_type_mappings' => array (
										'enum' => 'string'
								)
								
						)
						
				)
		),
		
		'router' => array (
				'routes' => array (
						
						'workflow' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/workflow',
										'defaults' => array (
												'__NAMESPACE__' => 'Workflow\Controller',
												'controller' => 'Index',
												'action' => 'index' 
										) 
								),
								'may_terminate' => true,
								'child_routes' => array (
										'default' => array (
												'type' => 'Segment',
												'options' => array (
														'route' => '/[:controller[/:action]]',
														'constraints' => array (
																'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
																'action' => '[a-zA-Z][a-zA-Z0-9_-]*' 
														),
														'defaults' => array () 
												) 
										) 
								) 
						) 
				
				) 
		
		),
		
		'service_manager' => array (
				'factories' => array (
						'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
						'Workflow\Service\WorkflowService' => 'Workflow\Service\WorkflowServiceFactory',
				) 
		),
		'translator' => array (
				'locale' => 'en_US',
				'translation_file_patterns' => array (
						array (
								'type' => 'gettext',
								'base_dir' => __DIR__ . '/../language',
								'pattern' => '%s.mo' 
						) 
				) 
		),
		'controllers' => array (
				'factories' => array (
						'Workflow\Controller\WF' => 'Workflow\Controller\WFControllerFactory',
				) 
		
		),
		'view_manager' => array (
				'display_not_found_reason' => true,
				'display_exceptions' => true,
				'doctype' => 'HTML5',
				'not_found_template' => 'error/404',
				'exception_template' => 'error/index',
				'template_map' => array (
						'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
						'user/index/index' => __DIR__ . '/../view/user/index/index.phtml',
						'error/404' => __DIR__ . '/../view/error/404.phtml',
						'error/index' => __DIR__ . '/../view/error/index.phtml',
						'layout/fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml' 
				),
				'template_path_stack' => array (
						__DIR__ . '/../view' 
				) 
		),
		
		// Plugin
		/*
		'controller_plugins' => array (
				'invokables' => array (
						'UserPlugin' => 'User\Controller\Plugin\UserPlugin' 
				) 
		) 
		*/
);
