<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array (
		'doctrine' => array (
				'driver' => array (
						'Application_driver' => array (
								'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
								'cache' => 'array',
								'paths' => array (
										__DIR__ . '/../src/Application/Entity' 
								) 
						),
						'orm_default' => array (
								'drivers' => array (
										'Application\Entity' => 'Application_driver' 
								) 
						) 
				) 
		),
		
		'router' => array (
				'routes' => array (
						
						// The following is a route to simplify getting started creating
						// new controllers and actions without needing to create a new
						// module. Simply drop new controllers in, and you can access them
						// using the path /application/:controller/:action
						'application' => array (
								'type' => 'Literal',
								'options' => array (
										'route' => '/application',
										'defaults' => array (
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Index',
												'action' => 'index' 
										) 
								) 
						
						),
						
						// For AclController
						'app_acl' => [ 
								'type' => 'segment',
								'options' => [ 
										'route' => '/application/acl[/:action]', // to update
										'constraints' => [ 
												'action' => '[a-zA-Z][a-zA-Z0-9_-]+' 
										],
										'defaults' => [ 
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Acl', // to update
												'action' => 'index' 
										] 
								] 
						],
						
						// For RoleController
						'app_role' => [ 
								'type' => 'segment',
								'options' => [ 
										'route' => '/application/role[/:action]', // to update
										'constraints' => [ 
												'action' => '[a-zA-Z][a-zA-Z0-9_-]+' 
										],
										'defaults' => [ 
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Role', // to update
												'action' => 'index' 
										] 
								] 
						],
						
						// For DepartmentController
						'app_department' => [ 
								'type' => 'segment',
								'options' => [ 
										'route' => '/application/department[/:action]', // to update
										'constraints' => [ 
												'action' => '[a-zA-Z][a-zA-Z0-9_-]+' 
										],
										'defaults' => [ 
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Department', // to update
												'action' => 'index' 
										] 
								] 
						],
						
						// For CountryController
						'app_country' => [ 
								'type' => 'segment',
								'options' => [ 
										'route' => '/application/country[/:action]', // to update
										'constraints' => [ 
												'action' => '[a-zA-Z][a-zA-Z0-9_-]+' 
										],
										'defaults' => [ 
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Country', // to update
												'action' => 'index' 
										] 
								] 
						],
						// For CountryController
						'app_currency' => [
								'type' => 'segment',
								'options' => [
										'route' => '/application/currency[/:action]', // to update
										'constraints' => [
												'action' => '[a-zA-Z][a-zA-Z0-9_-]+'
										],
										'defaults' => [
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Currency', // to update
												'action' => 'index'
										]
								]
						],
						// For CountryController
						'app_uom' => [
								'type' => 'segment',
								'options' => [
										'route' => '/application/uom[/:action]', // to update
										'constraints' => [
												'action' => '[a-zA-Z][a-zA-Z0-9_-]+'
										],
										'defaults' => [
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Uom', // to update
												'action' => 'index'
										]
								]
						],
						// For CompanyController
						'app_company' => [
								'type' => 'segment',
								'options' => [
										'route' => '/application/company[/:action]', // to update
										'constraints' => [
												'action' => '[a-zA-Z][a-zA-Z0-9_-]+'
										],
										'defaults' => [
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Company', // to update
												'action' => 'index'
										]
								]
						],
						// For PmtMethodeController
						'app_pmtmethod' => [
								'type' => 'segment',
								'options' => [
										'route' => '/application/pmt-method[/:action]', // to update
										'constraints' => [
												'action' => '[a-zA-Z][a-zA-Z0-9_-]+'
										],
										'defaults' => [
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'PmtMethod', // to update
												'action' => 'index'
										]
								]
						],
						// For PmtMethodeController
						'app_itemcategory' => [
								'type' => 'segment',
								'options' => [
										'route' => '/application/item-category[/:action]', // to update
										'constraints' => [
												'action' => '[a-zA-Z][a-zA-Z0-9_-]+'
										],
										'defaults' => [
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'ItemCategory', // to update
												'action' => 'index'
										]
								]
						],
						// For PmtMethodeController
						'app_search' => [
								'type' => 'segment',
								'options' => [
										'route' => '/application/search[/:action]', // to update
										'constraints' => [
												'action' => '[a-zA-Z][a-zA-Z0-9_-]+'
										],
										'defaults' => [
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Search', // to update
												'action' => 'index'
										]
								]
						],
						// For indexController
						'app_index' => [
								'type' => 'segment',
								'options' => [
										'route' => '/application/index[/:action]', // to update
										'constraints' => [
												'action' => '[a-zA-Z][a-zA-Z0-9_-]+'
										],
										'defaults' => [
												'__NAMESPACE__' => 'Application\Controller',
												'controller' => 'Index', // to update
												'action' => 'index'
										]
								]
						]
				) 
		),
		'service_manager' => array (
				'factories' => array (
						'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
						'Application\Service\ApplicationService' => 'Application\Service\ApplicationServiceFactory',
						'Application\Service\PdfService' => 'Application\Service\PdfServiceFactory',
						'Application\Service\ExcelService' => 'Application\Service\ExcelServiceFactory',
						'Application\Service\AclService' => 'Application\Service\AclServiceFactory',
						'Application\Service\DepartmentService' => 'Application\Service\DepartmentServiceFactory', 
						'Application\Service\ItemCategoryService' => 'Application\Service\ItemCategoryServiceFactory',
						'Application\Service\AppSearchService' => 'Application\Service\AppSearchServiceFactory',
						'Application\Listener\PictureUploadListener' =>'Application\Listener\PictureUploadListenerFactory',
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
				/**
				 * @deprecated in zf3
				 */
				'invokables' => array (						
						//'Application\Controller\Index' => 'Application\Controller\IndexController' 
				),
				
				'factories' => array (
						'Application\Controller\Index' => 'Application\Controller\IndexControllerFactory',
						
						'Application\Controller\Department' => 'Application\Controller\DepartmentControllerFactory',
						'Application\Controller\Acl' => 'Application\Controller\AclControllerFactory',
						'Application\Controller\Role' => 'Application\Controller\RoleControllerFactory',
						
						'Application\Controller\Country' => 'Application\Controller\CountryControllerFactory',
						'Application\Controller\Department' => 'Application\Controller\DepartmentControllerFactory',
						'Application\Controller\Currency' => 'Application\Controller\CurrencyControllerFactory',
						'Application\Controller\Uom' => 'Application\Controller\UomControllerFactory',
						'Application\Controller\Company' => 'Application\Controller\CompanyControllerFactory',
						'Application\Controller\PmtMethod' => 'Application\Controller\PmtMethodControllerFactory',
						'Application\Controller\ItemCategory' => 'Application\Controller\ItemCategoryControllerFactory',
						'Application\Controller\Search' => 'Application\Controller\SearchControllerFactory',
						
						
						
						'Application\Controller\Pdf' => 'Application\Controller\PdfControllerFactory',
						'Application\Controller\Company' => 'Application\Controller\CompanyControllerFactory' 
				
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
						'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
						'error/404' => __DIR__ . '/../view/error/404.phtml',
						'error/index' => __DIR__ . '/../view/error/index.phtml' 
				),
				'template_path_stack' => array (
						__DIR__ . '/../view' 
				) 
		) 
);
