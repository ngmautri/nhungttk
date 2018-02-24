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
				'hr_navi' => array (
						array (
								'label' => 'Home',
								'route' => 'application'
						),
						array (
								'label' => 'Create New Employee',
								'route' => 'hr/default',
								'controller' => 'employee',
								'action' => 'add',
								'icon' => 'glyphicon glyphicon-plus'
						),
						array (
								'label' => 'Employee List',
								'route' => 'hr/default',
								'controller' => 'employee',
								'action' => 'list',
								'icon' => 'glyphicon glyphicon-triangle-right'
						),
    				    array (
    				        'label' => 'Fingerscan', 
    				        'route' => 'hr/default',
    				        'controller' => 'fingerscan',
    				        'action' => 'list',
    				        'icon' => 'glyphicon glyphicon-triangle-right'
    				    )
				)
		),
		'router' => array (
				'routes' => array (
						// The following is a route to simplify getting started creating
						// new controllers and actions without needing to create a new
						// module. Simply drop new controllers in, and you can access them
						// using the path /application/:controller/:action
						'hr' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/hr',
										'defaults' => array (
												'__NAMESPACE__' => 'HR\Controller',
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
						'HR\Service\EmployeeSearchService' => 'HR\Service\EmployeeSearchServiceFactory',
						'hr_navi' => 'HR\Service\HrNavigationFactory', // <-- add this
						
				)
		),
		
		'controllers' => array (
				
				'factories' => array (
						// 'HR\Controller\Image' => 'HR\Controller\ImageControllerFactory',
					
						'HR\Controller\Index' => 'HR\Controller\IndexControllerFactory',
						'HR\Controller\Employee' => 'HR\Controller\EmployeeControllerFactory',
						'HR\Controller\EmployeePicture' => 'HR\Controller\EmployeePictureControllerFactory',
						'HR\Controller\EmployeeSearch' => 'HR\Controller\EmployeeSearchControllerFactory',
						'HR\Controller\EmployeeAttachment' => 'HR\Controller\EmployeeAttachmentControllerFactory',
				        'HR\Controller\EmployeeLeave' => 'HR\Controller\EmployeeLeaveControllerFactory',
				        'HR\Controller\EmployeeWarning' => 'HR\Controller\EmployeeWarningControllerFactory',
				        'HR\Controller\EmployeeEvaluation' => 'HR\Controller\EmployeeEvaluationControllerFactory',
				        'HR\Controller\EmployeeOT' => 'HR\Controller\EmployeeOTControllerFactory',
				        'HR\Controller\leaveReason' => 'HR\Controller\LeaveReasonControllerFactory',
				        'HR\Controller\Fingerscan' => 'HR\Controller\FingerscanControllerFactory',
	
				        'HR\Controller\Payroll' => 'HR\Controller\PayrollControllerFactory',
				        'HR\Controller\PayrollInput' => 'HR\Controller\PayrollInputControllerFactory',
				    
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
						'HR/layout-fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml',
						'hr/index/index' => __DIR__ . '/../view/hr/index/index.phtml',
						'error/404' => __DIR__ . '/../view/error/404.phtml',
						'error/index' => __DIR__ . '/../view/error/index.phtml' 
				),
				'template_path_stack' => array (
						__DIR__ . '/../view' 
				) 
		) 
);
