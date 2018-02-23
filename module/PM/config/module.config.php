<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array (
		'navigation' => array (
				'pm_navi' => array (
						array (
								'label' => 'Home',
								'route' => 'application'
						),
						array (
								'label' => 'Create New Project',
								'route' => 'pm/default',
								'controller' => 'project',
								'action' => 'add',
								'icon' => 'glyphicon glyphicon-plus'
						),
						array (
								'label' => 'Project List',
								'route' => 'pm/default',
								'controller' => 'project',
								'action' => 'list',
								'icon' => 'glyphicon glyphicon-triangle-right'
						)
				)
		),
		'router' => array (
				'routes' => array (
						
						'pm' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/pm',
										'defaults' => array (
												'__NAMESPACE__' => 'PM\Controller',
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
						'PM\Service\ProjectSearchService' => 'PM\Service\ProjectSearchServiceFactory',
						'pm_navi' => 'PM\Service\PmNavigationFactory', // <-- add this
						
				) 
		),
		'controllers' => array (
				'factories' => array (
						'PM\Controller\Index' => 'PM\Controller\IndexControllerFactory',
						'PM\Controller\Project' => 'PM\Controller\ProjectControllerFactory',
						'PM\Controller\ProjectAttachment' => 'PM\Controller\ProjectAttachmentControllerFactory',
						'PM\Controller\ProjectSearch' => 'PM\Controller\ProjectSearchControllerFactory',
				        'PM\Controller\ProjectItem' => 'PM\Controller\ProjectItemControllerFactory',
				    
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
						'PM/layout-fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml',
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
