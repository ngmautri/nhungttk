<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array (
		'router' => array (				
			'routes' => array (
					
				'user' => array (
						'type' => 'Literal',
						'options' => array (
								'route' => '/user',
								'defaults' => array (
										'__NAMESPACE__' => 'User\Controller',
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
					),
					
					'login' => array(
							'type' => 'literal',
							'options' => array(
									'route'    => '/user/auth/authenticate',
									'defaults' => array(
											'module' => 'User',
											'controller' => 'User\Controller\Auth',
											'action' => 'authenticate'
									),
							),
					),
					

					'register' => array(
							'type' => 'literal',
							'options' => array(
									'route'    => '/user/index/register',
									'defaults' => array(
											'module' => 'User',
											'controller' => 'User\Controller\Index',
											'action' => 'register'
									),
							),
					),
						
						
				)
									
		),
		
		'service_manager' => array (
				'factories' => array (
						'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory' 
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
				'invokables' => array (
						'User\Controller\Index' => 'User\Controller\IndexController',
						'User\Controller\Auth' => 'User\Controller\AuthController'
						
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
						'error/index' => __DIR__ . '/../view/error/index.phtml' 
				),
				'template_path_stack' => array (
						__DIR__ . '/../view' 
				) 
		),
		
		// Plugin
		'controller_plugins' => array (
				'invokables' => array (
						'NMTPlugin' => 'User\Controller\Plugin\NMTPlugin' 
				) 
		) 
);
