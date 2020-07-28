<?php

/**
 * @author Nguyen Mau Tri
 *
 */
use Inventory\Application\EventBus\Handler\Item\OnItemCreatedCreateIndex;
use Inventory\Application\EventBus\Handler\Item\OnItemUpdatedUpdateIndex;
use Inventory\Application\EventBus\Handler\Item\OnWhGrReversedCloseFiFoLayer;
use Inventory\Application\EventBus\Handler\Item\Factory\OnItemCreatedCreateIndexFactory;
use Inventory\Application\EventBus\Handler\Item\Factory\OnItemUpdatedUpdateIndexFactory;
use Inventory\Application\EventBus\Handler\Transaction\OnOpenBalancePostedCloseFifoLayer;
use Inventory\Application\EventBus\Handler\Transaction\OnOpenBalancePostedCloseTrx;
use Inventory\Application\EventBus\Handler\Transaction\OnWhGiPostedCalculateCost;
use Inventory\Application\EventBus\Handler\Transaction\OnWhGrPostedCreateFiFoLayer;
use Inventory\Application\EventBus\Handler\Transaction\OnWhGrPostedCreateSerialNo;
use Inventory\Application\EventBus\Handler\Transaction\Factory\OnOpenBalancePostedCloseFifoLayerFactory;
use Inventory\Application\EventBus\Handler\Transaction\Factory\OnOpenBalancePostedCloseTrxFactory;
use Inventory\Application\EventBus\Handler\Transaction\Factory\OnWhGiPostedCalculateCostFactory;
use Inventory\Application\EventBus\Handler\Transaction\Factory\OnWhGrPostedCreateFiFoLayerFactory;
use Inventory\Application\EventBus\Handler\Transaction\Factory\OnWhGrPostedCreateSerialNoFactory;
use Inventory\Application\EventBus\Handler\Transaction\Factory\OnWhGrReversedCloseFiFoLayerFactory;
use Inventory\Application\Eventbus\EventBusService;
use Inventory\Application\Eventbus\EventBusServiceFactory;
use Inventory\Application\Eventbus\HandlerMapper;
use Inventory\Application\Eventbus\HandlerMapperFactory;
use Inventory\Application\Reporting\Item\ItemReporter;
use Inventory\Application\Reporting\Item\ItemReporterFactory;
use Inventory\Application\Reporting\Transaction\TrxReporter;
use Inventory\Application\Reporting\Transaction\TrxReporterFactory;
use Inventory\Application\Service\Association\AssociationService;
use Inventory\Application\Service\Association\Factory\AssociationServiceFactory;
use Inventory\Application\Service\HSCode\HSCodeTreeService;
use Inventory\Application\Service\HSCode\Factory\HSCodeTreeServiceFactory;
use Inventory\Application\Service\HSCode\Tree\HSCodeTree;
use Inventory\Application\Service\HSCode\Tree\Factory\HSCodeTreeFactory;
use Inventory\Application\Service\Item\ItemService;
use Inventory\Application\Service\Item\Factory\ItemServiceFactory;
use Inventory\Application\Service\MfgCatalog\Tree\MfgCatalogTree;
use Inventory\Application\Service\MfgCatalog\Tree\Factory\MfgCatalogTreeFactory;
use Inventory\Application\Service\Search\ZendSearch\HSCode\HSCodeSearchIndexImpl;
use Inventory\Application\Service\Search\ZendSearch\HSCode\HSCodeSearchIndexImplFactory;
use Inventory\Application\Service\Search\ZendSearch\HSCode\HSCodeSearchQueryImpl;
use Inventory\Application\Service\Search\ZendSearch\HSCode\HSCodeSearchQueryImplFactory;
use Inventory\Application\Service\Search\ZendSearch\Item\ItemSearchIndexImpl;
use Inventory\Application\Service\Search\ZendSearch\Item\ItemSearchIndexImplFactory;
use Inventory\Application\Service\Search\ZendSearch\Item\ItemSearchQueryImpl;
use Inventory\Application\Service\Search\ZendSearch\Item\ItemSearchQueryImplFactory;
use Inventory\Application\Service\Transaction\TrxService;
use Inventory\Application\Service\Transaction\TrxServiceFactory;
use Inventory\Application\Service\Upload\ItemSerialUploadService;
use Inventory\Application\Service\Upload\ItemSerialUploadServiceFactory;
use Inventory\Application\Service\Upload\HSCode\HSCodeUpload;
use Inventory\Application\Service\Upload\HSCode\HSCodeUploadServiceFactory;
use Inventory\Infrastructure\Persistence\Doctrine\ItemReportRepositoryImpl;
use Inventory\Infrastructure\Persistence\Doctrine\TrxReportRepositoryImpl;
use Inventory\Infrastructure\Persistence\Doctrine\Factory\ItemReportRepositoryImplFactory;
use Inventory\Infrastructure\Persistence\Doctrine\Factory\TrxReportRepositoryImplFactory;

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
                'action' => 'create',
                'icon' => 'glyphicon glyphicon-plus'
            ),

            array(
                'label' => 'Item List',
                'route' => 'Inventory/default',
                'controller' => 'item',
                'action' => 'list2',
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
                'label' => 'Manufactuer Catalog',
                'route' => 'Inventory/default',
                'controller' => 'mfg-catalog',
                'action' => 'tree',
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
                'label' => 'Item Variant',
                'route' => 'Inventory/default',
                'controller' => 'item-variant',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-list'
            ),

            array(
                'label' => 'HS Code',
                'route' => 'Inventory/default',
                'controller' => 'hs-code',
                'action' => 'tree',
                'icon' => 'glyphicon glyphicon-list'
            ),

            array(
                'label' => 'Reporting',
                'route' => 'Inventory/default',
                'controller' => 'report',
                'action' => 'index',
                'icon' => 'fa fa-bar-chart'
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
            )
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
            ),

            'item-api' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/inventory/item-api[/:id]',
                    'constraints' => array(
                        'id' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Inventory\API\ItemController'
                    )
                )
            ),

            'warehouse-api' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/inventory/wh-api[/:id]',
                    'constraints' => array(
                        'id' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Inventory\API\WarehouseController'
                    )
                )
            ),

            'inventory-trx-api' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/inventory/trx-api[/:id]',
                    'constraints' => array(
                        'id' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Inventory\API\TransactionController'
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
            'Inventory\Service\OpeningBalanceService' => 'Inventory\Service\OpeningBalanceServiceFactory',
            'Inventory\Service\ItemSerialService' => 'Inventory\Service\ItemSerialServiceFactory',
            'Inventory\Service\WarehouseService' => 'Inventory\Service\WarehouseServiceFactory',
            'Inventory\Service\WarehouseLocationService' => 'Inventory\Service\WarehouseLocationServiceFactory',
            'Inventory\Service\ItemVariantService' => 'Inventory\Service\ItemVariantServiceFactory',
            'Inventory\Service\ItemGroupService' => 'Inventory\Service\ItemGroupServiceFactory',

            'Inventory\Service\InventoryTransactionService' => 'Inventory\Service\InventoryTransactionServiceFactory',
            'Inventory\Service\Report\ItemReportService' => 'Inventory\Service\Report\ItemReportServiceFactory',
            'Inventory\Service\Report\ItemReportService' => 'Inventory\Service\Report\ItemReportServiceFactory',

            // Services:
            'Inventory\Application\Service\Item\ItemCRUDService' => 'Inventory\Application\Service\Item\ItemCRUDServiceFactory',
            'Inventory\Application\Service\Item\FIFOService' => 'Inventory\Application\Service\Item\FIFOServiceFactory',
            'Inventory\Application\Service\Item\ItemCategoryService' => 'Inventory\Application\Service\Item\ItemCategoryServiceFactory',
            'Inventory\Application\Service\Warehouse\TransactionService' => 'Inventory\Application\Service\Warehouse\TransactionServiceFactory',
            'Inventory\Application\Service\Warehouse\WarehouseService' => 'Inventory\Application\Service\Warehouse\WarehouseServiceFactory',
            'Inventory\Application\Event\Listener\ItemCreatedEventListener' => 'Inventory\Application\Event\Listener\ItemCreatedEventListenerFactory',
            'Inventory\Application\Event\Listener\ItemUpdatedEventListener' => 'Inventory\Application\Event\Listener\ItemUpdatedEventListenerFactory',
            'Inventory\Application\Event\Listener\ItemLoggingListener' => 'Inventory\Application\Event\Listener\ItemLoggingListenerFactory',
            'Inventory\Application\Event\Listener\WarehouseLoggingListener' => 'Inventory\Application\Event\Listener\WarehouseLoggingListenerFactory',
            'Inventory\Application\Service\Search\ZendSearch\ItemSearchService' => 'Inventory\Application\Service\Search\ZendSearch\ItemSearchServiceFactory',
            ItemService::class => ItemServiceFactory::class,
            AssociationService::class => AssociationServiceFactory::class,
            ItemSerialUploadService::class => ItemSerialUploadServiceFactory::class,
            HSCodeUpload::class => HSCodeUploadServiceFactory::class,
            HSCodeTreeService::class => HSCodeTreeServiceFactory::class,
            HSCodeTree::class => HSCodeTreeFactory::class,
            MfgCatalogTree::class => MfgCatalogTreeFactory::class,
            TrxService::class => TrxServiceFactory::class,

            // Reporting
            ItemReporter::class => ItemReporterFactory::class,
            TrxReporter::class => TrxReporterFactory::class,

            // Repository
            'Inventory\Infrastructure\Persistence\DoctrineItemReportingRepository' => 'Inventory\Infrastructure\Persistence\Factory\DoctrineItemReportingRepositoryFactory',
            'Inventory\Infrastructure\Persistence\DoctrineItemListRepository' => 'Inventory\Infrastructure\Persistence\Factory\DoctrineItemListRepositoryFactory',
            'Inventory\Infrastructure\Persistence\Doctrine\ItemCategoryRepositoryImpl' => 'Inventory\Infrastructure\Persistence\Doctrine\Factory\ItemCategoryRepositoryImplFactory',
            ItemReportRepositoryImpl::class => ItemReportRepositoryImplFactory::class,
            TrxReportRepositoryImpl::class => TrxReportRepositoryImplFactory::class,

            // Search Handler
            ItemSearchIndexImpl::class => ItemSearchIndexImplFactory::class,
            ItemSearchQueryImpl::class => ItemSearchQueryImplFactory::class,

            HSCodeSearchIndexImpl::class => HSCodeSearchIndexImplFactory::class,
            HSCodeSearchQueryImpl::class => HSCodeSearchQueryImplFactory::class,

            // Event Handler Resolver
            EventBusService::class => EventBusServiceFactory::class,
            HandlerMapper::class => HandlerMapperFactory::class,

            // Event Handler// ITEM
            OnItemCreatedCreateIndex::class => OnItemCreatedCreateIndexFactory::class,
            OnItemUpdatedUpdateIndex::class => OnItemUpdatedUpdateIndexFactory::class,

            // Event Handler// Transaction
            OnWhGiPostedCalculateCost::class => OnWhGiPostedCalculateCostFactory::class,
            OnWhGrPostedCreateFiFoLayer::class => OnWhGrPostedCreateFiFoLayerFactory::class,
            OnWhGrPostedCreateSerialNo::class => OnWhGrPostedCreateSerialNoFactory::class,
            OnWhGrReversedCloseFiFoLayer::class => OnWhGrReversedCloseFiFoLayerFactory::class,
            OnOpenBalancePostedCloseFifoLayer::class => OnOpenBalancePostedCloseFifoLayerFactory::class,
            OnOpenBalancePostedCloseTrx::class => OnOpenBalancePostedCloseTrxFactory::class
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
            'Inventory\Controller\ItemBatch' => 'Inventory\Controller\ItemBatchControllerFactory',
            'Inventory\Controller\GR' => 'Inventory\Controller\GRControllerFactory',
            'Inventory\Controller\GI' => 'Inventory\Controller\GIControllerFactory',
            'Inventory\Controller\GIRow' => 'Inventory\Controller\GIRowControllerFactory',

            'Inventory\Controller\Transfer' => 'Inventory\Controller\TransferControllerFactory',
            'Inventory\Controller\TransferRow' => 'Inventory\Controller\TransferRowControllerFactory',

            'Inventory\Controller\ChangeLog' => 'Inventory\Controller\ChangeLogControllerFactory',
            'Inventory\Controller\ActivityLog' => 'Inventory\Controller\ActivityLogControllerFactory',
            'Inventory\Controller\Dashboard' => 'Inventory\Controller\DashboardControllerFactory',

            'Inventory\Controller\OpeningBalance' => 'Inventory\Controller\OpeningBalanceControllerFactory',
            'Inventory\Controller\OpeningBalanceRow' => 'Inventory\Controller\OpeningBalanceRowControllerFactory',

            'Inventory\Controller\Stock' => 'Inventory\Controller\StockControllerFactory',

            'Inventory\Controller\Setting' => 'Inventory\Controller\SettingControllerFactory',

            'Inventory\Controller\ItemGroup' => 'Inventory\Controller\ItemGroupControllerFactory',
            'Inventory\Controller\ItemAccounting' => 'Inventory\Controller\ItemAccountingControllerFactory',
            'Inventory\Controller\ItemVariant' => 'Inventory\Controller\ItemVariantControllerFactory',

            'Inventory\Controller\SimilarItem' => 'Inventory\Controller\SimilarItemControllerFactory',
            'Inventory\Controller\Report' => 'Inventory\Controller\ReportControllerFactory',

            'Inventory\Controller\Association' => 'Inventory\Controller\AssociationControllerFactory',
            'Inventory\Controller\AssociationItem' => 'Inventory\Controller\AssociationItemControllerFactory',

            'Inventory\Controller\HSCode' => 'Inventory\Controller\HSCodeControllerFactory',
            'Inventory\Controller\MfgCatalog' => 'Inventory\Controller\MfgCatalogControllerFactory',
            'Inventory\Controller\ItemReport' => 'Inventory\Controller\ItemReportControllerFactory',
            'Inventory\Controller\TrxReport' => 'Inventory\Controller\TrxReportControllerFactory',

            // API
            'Inventory\API\ItemController' => 'Inventory\API\ItemControllerFactory',
            'Inventory\API\WarehouseController' => 'Inventory\API\WarehouseControllerFactory',
            'Inventory\API\TransactionController' => 'Inventory\API\TransactionControllerFactory'
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
            'Inventory/gi-create-layout' => __DIR__ . '/../view/layout/create_gi_layout.phtml',
            'Inventory/layout-fullscreen' => __DIR__ . '/../view/layout/layout-fullscreen.phtml',
            'Inventory/layout-blank' => __DIR__ . '/../view/layout/layout-blank.phtml',

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