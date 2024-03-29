<?php
/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
return array(
    'navigation' => array(
        'finance_navi' => array(
            array(
                'label' => 'Home',
                'route' => 'application',
                'icon' => 'glyphicon glyphicon-home'
            ),
            /*
			array(
                'label' => 'Add New Period',
                'route' => 'finance/default',
                'controller' => 'posting-period',
                'action' => 'add',
                'icon' => 'glyphicon glyphicon-plus'
            ),*/
            array(
                'label' => 'New A/P Invoice',
                'route' => 'finance/default',
                'controller' => 'v-invoice',
                'action' => 'add',
                'icon' => 'glyphicon glyphicon-plus'
            ),
            array(
                'label' => 'A/P Invoice List',
                'route' => 'finance/default',
                'controller' => 'v-invoice',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),

            array(
                'label' => 'A/P Report',
                'route' => 'procure/default',
                'controller' => 'ap-report',
                'action' => 'row-status',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),

            array(
                'label' => 'Log',
                'route' => 'finance/default',
                'controller' => 'activity-log',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            )
        )
    ),

    'router' => array(
        'routes' => array(

            'finance' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/finance',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Finance\Controller',
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
            'finance_navi' => 'Finance\Service\FinanceNavigationFactory', // <-- add this
                                                                           // 'Finance\Service\APInvoiceService' => 'Finance\Service\APInvoiceServiceFactory',
            'Finance\Service\JEService' => 'Finance\Service\JEServiceFactory',
            'Finance\Service\AccountService' => 'Finance\Service\AccountServiceFactory'
        )
    ),

    'controllers' => array(
        'factories' => array(
            'Finance\Controller\Index' => 'Finance\Controller\IndexControllerFactory',
            'Finance\Controller\PostingPeriod' => 'Finance\Controller\PostingPeriodControllerFactory',

            // to delete
            'Finance\Controller\VInvoice' => 'Finance\Controller\VInvoiceControllerFactory',
            'Finance\Controller\VInvoiceRow' => 'Finance\Controller\VInvoiceRowControllerFactory',

            'Finance\Controller\VInvoiceAttachment' => 'Finance\Controller\VInvoiceAttachmentControllerFactory',
            'Finance\Controller\ActivityLog' => 'Finance\Controller\ActivityLogControllerFactory',
            'Finance\Controller\ChangeLog' => 'Finance\Controller\ChangeLogControllerFactory',
            'Finance\Controller\Fx' => 'Finance\Controller\FxControllerFactory',
            'Finance\Controller\Account' => 'Finance\Controller\AccountControllerFactory',
            'Finance\Controller\JE' => 'Finance\Controller\JEControllerFactory',
            'Finance\Controller\JERow' => 'Finance\Controller\JERowControllerFactory'
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'Finance/layout-fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml',
            'Finance/layout-fullscreen' => __DIR__ . '/../view/layout/layout-fullscreen.phtml',
            'Finance/index/index' => __DIR__ . '/../view/procure/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'layout/fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    )
);
