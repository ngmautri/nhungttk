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
						
						'procure' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/procure',
										'defaults' => array (
												'__NAMESPACE__' => 'Procure\Controller',
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
						'Procure\Controller\Index' => 'Procure\Controller\IndexControllerFactory',
						'Procure\Controller\Pr' => 'Procure\Controller\PrControllerFactory',
						'Procure\Controller\PrAttachment' => 'Procure\Controller\PrAttachmentControllerFactory',
						'Procure\Controller\PrRow' => 'Procure\Controller\PrRowControllerFactory',
						
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
						'procure/index/index' => __DIR__ . '/../view/procure/index/index.phtml',
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
