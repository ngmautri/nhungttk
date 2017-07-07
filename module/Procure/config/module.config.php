<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array (
		'navigation' => array(
				'procure_navi' => array(
						array(
								'label' => 'Home',
								'route' => 'application',
								'icon' => 'glyphicon glyphicon-home',
						),
						array(
								'label' => 'Add New PR',
								'route' => 'procure/default',
								'controller' => 'pr',
								'action' => 'add',
								'icon' => 'glyphicon glyphicon-plus',
						),
						
						array(
								'label' => 'PR List',
								//'route' => 'procure/default',
						        //'url' ="/procure/pr/all?pr_year=0&balance=0&is_active=1&sort_by=prNumber&sort=ASC&perPage=15",
								'controller' => 'pr',
								'action' => 'all',
								'icon' => 'glyphicon glyphicon-list',
						),
						
    				 	array(
								'label' => 'PR Row',
								'route' => 'procure/default',
								'controller' => 'pr-row',
								'action' => 'all',
								'icon' => 'glyphicon glyphicon-list',
								
						),
				),
		),
		
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
						'procure_navi' => 'Procure\Service\ProcureNavigationFactory', // <-- add this
						
						
						'Procure\Service\PrSearchService' => 'Procure\Service\PrSearchServiceFactory',
						
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
						'Procure\Controller\PrSearch' => 'Procure\Controller\PrSearchControllerFactory',
						
				) 
		
		),
		'view_manager' => array (
				'display_not_found_reason' => true,
				'display_exceptions' => true,
				'doctype' => 'HTML5',
				'not_found_template' => 'error/404',
				'exception_template' => 'error/index',
				'template_map' => array (
						'Procure/layout-fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml',
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
		'controller_plugins' => array (
		    'factories' => array (
		        'ProcureWfPlugin' => 'Procure\Controller\Plugin\WfPluginFactory',
		    ) 
		) 
	
);
