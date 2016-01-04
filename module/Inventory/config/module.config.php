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
						'home' => array (
								'type' => 'Zend\Mvc\Router\Http\Literal',
								'options' => array (
										'route' => '/',
										'defaults' => array (
												'controller' => 'Inventory\Controller\Asset',
												'action' => 'Index' 
										) 
								)
									
								
						),
						
						'assetcategory' => array(
								'type' => 'literal',
								'options' => array(
										'route'    => '/inventory/asset/category',
										'defaults' => array(
												'__NAMESPACE__' => 'Inventory\Controller',
												'controller' => 'Asset',
												'action' => 'index' 
										),
								),
						),
						
						// The following is a route to simplify getting started creating
						// new controllers and actions without needing to create a new
						// module. Simply drop new controllers in, and you can access them
						// using the path /application/:controller/:action
						'inventory' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/inventory',
										'defaults' => array (
												'__NAMESPACE__' => 'Inventory\Controller',
												'controller' => 'Asset',
												'action' => 'category' 
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
						'Inventory\Controller\Index' => 'Inventory\Controller\IndexController',
						'Inventory\Controller\Asset' => 'Inventory\Controller\AssetController',
						'Inventory\Controller\Spareparts' => 'Inventory\Controller\SparepartsController',
						'Inventory\Controller\Image' => 'Inventory\Controller\ImageController'
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
						'inventory/index/index' => __DIR__ . '/../view/inventory/index/index.phtml',
						'error/404' => __DIR__ . '/../view/error/404.phtml',
						'error/index' => __DIR__ . '/../view/error/index.phtml' 
				),
				'template_path_stack' => array (
						__DIR__ . '/../view' 
				) 
		),
		
		// Plugin
		'controller_plugins' => array ()
		
		// blank
		
		 
);