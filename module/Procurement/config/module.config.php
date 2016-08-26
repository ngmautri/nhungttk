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
						
						'procurement' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/procurement',
										'defaults' => array (
												'__NAMESPACE__' => 'Procurement\Controller',
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
						
						'create-pr-step2' => array(
								'type' => 'Zend\Mvc\Router\Http\Segment',
								'options' => array(
										'route'   => '/pr/create-step2[?pr=:pr]',
										'defaults' => array(
												'controller' => 'Procurement\Controller\PR',
												'action'     => 'create-step2',
										)
								)
						),
				) 
		),
		
		'controllers' => array (
				'invokables' => array (
						'Procurement\Controller\Index' => 'Procurement\Controller\IndexController',
				),
				'factories' => array (
						'Procurement\Controller\PR' => 'Procurement\Controller\PRControllerFactory',
						'Procurement\Controller\PO' => 'Procurement\Controller\POControllerFactory',
						'Procurement\Controller\GR' => 'Procurement\Controller\GRControllerFactory',
						'Procurement\Controller\DO' => 'Procurement\Controller\DOControllerFactory',
						'Procurement\Controller\Cash' => 'Procurement\Controller\CashControllerFactory',
						'Procurement\Controller\Delivery' => 'Procurement\Controller\DeliveryControllerFactory',
						'Procurement\Controller\Vendor' => 'Procurement\Controller\VendorControllerFactory',
						
				)
		),
		'view_manager' => array (
				'display_not_found_reason' => true,
				'display_exceptions' => true,
				'doctype' => 'HTML5',
				'not_found_template' => 'error/404',
				'exception_template' => 'error/index',
				'template_map' => array (
						'layout1/layout' => __DIR__ . '/../view/layout/layout.phtml',
						'procurement/index/index' => __DIR__ . '/../view/procurement/index/index.phtml',
						'error/404' => __DIR__ . '/../view/error/404.phtml',
						'error/index' => __DIR__ . '/../view/error/index.phtml' 
				),
				'template_path_stack' => array (
						__DIR__ . '/../view' 
				) 
		) 
);
