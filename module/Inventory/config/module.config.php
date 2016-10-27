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
												'controller' => 'Inventory\Controller\Index',
												'action' => 'index'
										)
								)
						),
						
						
						// The following is a route to simplify getting started creating
						// new controllers and actions without needing to create a new
						// module. Simply drop new controllers in, and you can access them
						// using the path /application/:controller/:action
						'Inventory' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/inventory',
										'defaults' => array (
												'__NAMESPACE__' => 'Inventory\Controller',
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
							
				'Asset_Category' => array (
						'type' => 'literal',
						'options' => array (
								'route' => '/inventory/asset/category',
								'defaults' => array (
										'__NAMESPACE__' => 'Inventory\Controller',
										'controller' => 'Asset',
										'action' => 'category' 
								) 
						) 
				),
				
				'Spareparts_Category' => array (
						'type' => 'literal',
						'options' => array (
								'route' => '/inventory/spareparts/category',
								'defaults' => array (
										'__NAMESPACE__' => 'Inventory\Controller',
										'controller' => 'Spareparts',
										'action' => 'category' 
								) 
						),
				),
						

			),
						
		),
		
		'console' => array(
				'router' => array(
						'routes' => array(									
								'order_suggestion_console' => array(
										'options' => array(
												'route'    => 'suggest',
												'defaults' => array(
														'controller' => 'Inventory\Controller\Console',
														'action'     => 'suggest'
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
						'Inventory\Controller\Image' => 'Inventory\Controller\ImageController',
				),
				
				'factories' => array (
						'Inventory\Controller\Admin' => 'Inventory\Controller\AdminControllerFactory',
						'Inventory\Controller\Spareparts' => 'Inventory\Controller\SparepartsControllerFactory',
						'Inventory\Controller\Report' => 'Inventory\Controller\ReportControllerFactory',
						'Inventory\Controller\Count' => 'Inventory\Controller\CountControllerFactory',
						'Inventory\Controller\Article' => 'Inventory\Controller\ArticleControllerFactory',
						'Inventory\Controller\Search' => 'Inventory\Controller\SearchControllerFactory',
						'Inventory\Controller\Console' => 'Inventory\Controller\ConsoleControllerFactory',
						'Inventory\Controller\Purchasing' => 'Inventory\Controller\PurchasingControllerFactory',
						'Inventory\Controller\Warehouse' => 'Inventory\Controller\WarehouseControllerFactory',
						
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
						'layout/no-layout' => __DIR__ . '/../view/layout/no-layout.phtml',
						'inventory/index/index' => __DIR__ . '/../view/inventory/index/index.phtml',
						'error/404' => __DIR__ . '/../view/error/404.phtml',
						'error/index' => __DIR__ . '/../view/error/index.phtml',
						'layout/inventory/print' => __DIR__ . '/../view/layout/print.phtml',
						'layout/inventory/ajax' => __DIR__ . '/../view/layout/ajax.phtml',
				),
				'template_path_stack' => array (
						__DIR__ . '/../view' 
				) ,				
				'ViewJsonStrategy'
		),
		
		// Plugin
		'controller_plugins' => array (
	)
		
		// blank
		
		 
);
