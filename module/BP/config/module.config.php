<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    
  
    'navigation' => array(
        'bp_navi' => array(
            array(
                'label' => 'Home',
                'route' => 'application'
            ),
            array(
                'label' => 'Create New Vendor',
                'route' => 'bp/default',
                'controller' => 'vendor',
                'action' => 'add',
                'icon' => 'glyphicon glyphicon-plus'
            ),
            array(
                'label' => 'Vendor',
                'route' => 'bp/default',
                'controller' => 'vendor',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),
            array(
                'label' => 'Log',
                'route' => 'bp/default',
                'controller' => 'activity-log',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-list'
                
            )
        )
    ),
    
    'router' => array(
        'routes' => array(
            
            'bp' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/bp',
                    'defaults' => array(
                        '__NAMESPACE__' => 'BP\Controller',
                        'controller' => 'Index',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array()
                        )
                    )
                )
            )
        
        )
    
    ),
    
    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'bp_navi' => 'BP\Service\BpNavigationFactory', // <-- add this
            
            'BP\Service\VendorSearchService' => 'BP\Service\VendorSearchServiceFactory'
        
        )
    ),
    
    'controllers' => array(
        'factories' => array(
            'BP\Controller\Vendor' => 'BP\Controller\VendorControllerFactory',
            'BP\Controller\VendorContract' => 'BP\Controller\VendorContractControllerFactory',
            'BP\Controller\VendorSearch' => 'BP\Controller\VendorSearchControllerFactory',
            'BP\Controller\VendorAttachment' => 'BP\Controller\VendorAttachmentControllerFactory',
            
            'BP\Controller\Debtor' => 'BP\Controller\DebtorControllerFactory',
            'BP\Controller\DebtorContract' => 'BP\Controller\DebtorContractControllerFactory',
            
            'BP\Controller\ChangeLog' => 'BP\Controller\ChangeLogControllerFactory',
            'BP\Controller\ActivityLog' => 'BP\Controller\ActivityLogControllerFactory',
            
        )
    
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'BP/layout-fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml',
            'bp/index/index' => __DIR__ . '/../view/bp/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'layout/fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    )
   
);
