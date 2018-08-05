<?php

/**
 * @author Nguyen Mau Tri
 *
 */
return array(
    'navigation' => array(
        'inventory_navi' => array(
            array(
                'label' => 'Dashboard',
                'route' => 'Inventory/default',
                'controller' => 'dashboard',
                'action' => 'index',
                'icon' => 'glyphicon glyphicon-home'
            ),
            array(
                'label' => 'Add New Item',
                'route' => 'Inventory/default',
                'controller' => 'item',
                'action' => 'add',
                'icon' => 'glyphicon glyphicon-plus'
            ),

            array(
                'label' => 'Item List',
                'route' => 'Inventory/default',
                'controller' => 'item',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-list'
            ),
            array(
                'label' => 'Item Price',
                'route' => 'Inventory/default',
                'controller' => 'item',
                'action' => 'item-price',
                'icon' => 'glyphicon glyphicon-list'
            ),

            array(
                'label' => 'Item Category',
                'route' => 'Inventory/default',
                'controller' => 'item-category',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-list'
            ),

            array(
                'label' => 'Item Transaction',
                'route' => 'Inventory/default',
                'controller' => 'item-transaction',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-list'
            ),
            array(
                'label' => 'Item Serial',
                'route' => 'Inventory/default',
                'controller' => 'item-serial',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-list'
            ),
            array(
                'label' => 'Serial Number',
                'route' => 'Inventory/default',
                'controller' => 'serial-number',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-list'
            ),

            array(
                'label' => 'Batch Number',
                'route' => 'Inventory/default',
                'controller' => 'batch-number',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-list'
            ),

            array(
                'label' => 'Log',
                'route' => 'Inventory/default',
                'controller' => 'activity-log',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-list'
            ),
            
            array(
                'label' => 'Setup',
                'route' => 'Inventory/default',
                'controller' => 'setting',
                'action' => 'index',
                'icon' => 'glyphicon glyphicon-list'
            ),
        )
    ),

    'router' => array(
        'routes' => array(

            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Inventory\Controller\Index',
                        'action' => 'index'
                    )
                )
            ),

            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'Inventory' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/inventory',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Inventory\Controller',
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

    /* 'console' => array(
        'router' => array(
            'routes' => array(
                'order_suggestion_console' => array(
                    'options' => array(
                        'route' => 'suggest',
                        'defaults' => array(
                            'controller' => 'Inventory\Controller\Console',
                            'action' => 'suggest'
                        )
                    )
                )
            )
        )
    ), */

    'service_manager' => array(
        'factories' => array(

            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',

            // navagation
            'inventory_navi' => 'Inventory\Service\InventoryNavigationFactory',

            'Inventory\Service\ItemSearchService' => 'Inventory\Service\ItemSearchServiceFactory',
            'Inventory\Service\ItemSerialSearchService' => 'Inventory\Service\ItemSerialSearchServiceFactory',
             'Inventory\Service\FIFOLayerService' => 'Inventory\Service\FIFOLayerServiceFactory',            
            'Inventory\Service\GIService' => 'Inventory\Service\GIServiceFactory',
            
        )
    ),

    'controllers' => array(

        'factories' => array(
            'Inventory\Controller\Index' => 'Inventory\Controller\IndexControllerFactory',
            'Inventory\Controller\Search' => 'Inventory\Controller\SearchControllerFactory',
            'Inventory\Controller\Warehouse' => 'Inventory\Controller\WarehouseControllerFactory',

            // refactory

            'Inventory\Controller\Item' => 'Inventory\Controller\ItemControllerFactory',
            'Inventory\Controller\Category' => 'Inventory\Controller\CategoryControllerFactory',
            'Inventory\Controller\ItemAttachment' => 'Inventory\Controller\ItemAttachmentControllerFactory',
            'Inventory\Controller\ItemPurchase' => 'Inventory\Controller\ItemPurchaseControllerFactory',
            'Inventory\Controller\ItemPicture' => 'Inventory\Controller\ItemPictureControllerFactory',
            'Inventory\Controller\ItemSearch' => 'Inventory\Controller\ItemSearchControllerFactory',
            'Inventory\Controller\ItemTransaction' => 'Inventory\Controller\ItemTransactionControllerFactory',
            'Inventory\Controller\ItemAssignment' => 'Inventory\Controller\ItemAssignmentControllerFactory',
            'Inventory\Controller\ItemCategory' => 'Inventory\Controller\ItemCategoryControllerFactory',
            'Inventory\Controller\SerialNumber' => 'Inventory\Controller\SerialNumberControllerFactory',
            'Inventory\Controller\BatchNumber' => 'Inventory\Controller\BatchNumberControllerFactory',
            'Inventory\Controller\ItemSerial' => 'Inventory\Controller\ItemSerialControllerFactory',
            'Inventory\Controller\ItemSerialAttachment' => 'Inventory\Controller\ItemSerialAttachmentControllerFactory',
            'Inventory\Controller\ItemBatch' => 'Inventory\Controller\ItemBatchControllerFactory',            'Inventory\Controller\GR' => 'Inventory\Controller\GRControllerFactory',
            'Inventory\Controller\GI' => 'Inventory\Controller\GIControllerFactory',
            'Inventory\Controller\Transfer' => 'Inventory\Controller\TransferControllerFactory',
            'Inventory\Controller\ChangeLog' => 'Inventory\Controller\ChangeLogControllerFactory',
            'Inventory\Controller\ActivityLog' => 'Inventory\Controller\ActivityLogControllerFactory',
            'Inventory\Controller\Dashboard' => 'Inventory\Controller\DashboardControllerFactory',
            'Inventory\Controller\OpeningBalance' => 'Inventory\Controller\OpeningBalanceControllerFactory',
            'Inventory\Controller\Setting' => 'Inventory\Controller\SettingControllerFactory',
            
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            // 'inventory/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'layout/layout' => __DIR__ . '/../view/layout/layout-fluid.phtml',
            'Inventory/layout-fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml',
            'layout/no-layout' => __DIR__ . '/../view/layout/no-layout.phtml',
            'inventory/index/index' => __DIR__ . '/../view/inventory/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'layout/inventory/print' => __DIR__ . '/../view/layout/print.phtml',
            'layout/inventory/ajax' => __DIR__ . '/../view/layout/ajax.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ),
        'ViewJsonStrategy'
    )
);