<?php

/**
 * Configuration file.
 * @author Ngyuyen Mau Tri
 */
use Procure\Application\EventBus\EventBusService;
use Procure\Application\EventBus\EventBusServiceFactory;
use Procure\Application\EventBus\HandlerMapper;
use Procure\Application\EventBus\HandlerMapperFactory;
use Procure\Application\EventBus\Handler\AP\UpdateIndexOnApPosted;
use Procure\Application\EventBus\Handler\AP\UpdateIndexOnApPostedFactory;
use Procure\Application\EventBus\Handler\GR\CreateGrOnApPosted;
use Procure\Application\EventBus\Handler\GR\CreateGrReversalOnApReversed;
use Procure\Application\EventBus\Handler\GR\OnApPostedCreateGrByWarehouse;
use Procure\Application\EventBus\Handler\GR\OnApReversedCreateGrReversalByWarehouse;
use Procure\Application\EventBus\Handler\GR\Factory\CreateGrOnApPostedFactory;
use Procure\Application\EventBus\Handler\GR\Factory\CreateGrReversalOnApReversedFactory;
use Procure\Application\EventBus\Handler\GR\Factory\OnApPostedCreateGrByWarehouseFactory;
use Procure\Application\EventBus\Handler\GR\Factory\OnApReversedCreateGrReversalByWarehouseFactory;
use Procure\Application\EventBus\Handler\PO\UpdateIndexOnPoPosted;
use Procure\Application\EventBus\Handler\PO\UpdateIndexOnPoPostedFactory;
use Procure\Application\EventBus\Handler\PR\UpdateIndexOnPrSubmitted;
use Procure\Application\EventBus\Handler\PR\UpdateIndexOnPrSubmittedFactory;
use Procure\Application\EventBus\Handler\QR\UpdateIndexOnQrPosted;
use Procure\Application\EventBus\Handler\QR\UpdateIndexOnQrPostedFactory;
use Procure\Application\Reporting\GR\GrReporter;
use Procure\Application\Reporting\GR\GrReporterFactory;
use Procure\Application\Reporting\PR\PrReporter;
use Procure\Application\Reporting\PR\PrReporterFactory;
use Procure\Application\Reporting\QR\QrReporter;
use Procure\Application\Reporting\QR\QrReporterFactory;
use Procure\Application\Service\PR\PRServiceFactory;
use Procure\Application\Service\PR\PRServiceFactoryV2;
use Procure\Application\Service\PR\PRServiceV2;
use Procure\Application\Service\QR\QRService;
use Procure\Application\Service\QR\QRServiceFactory;
use Procure\Application\Service\Search\ZendSearch\PR\PrSearchIndexImpl;
use Procure\Application\Service\Search\ZendSearch\PR\PrSearchIndexImplFactory;
use Procure\Application\Service\Search\ZendSearch\PR\PrSearchQueryImpl;
use Procure\Application\Service\Search\ZendSearch\PR\PrSearchQueryImplFactory;
use Procure\Application\Service\Search\ZendSearch\QR\QrSearchIndexImpl;
use Procure\Application\Service\Search\ZendSearch\QR\QrSearchIndexImplFactory;
use Procure\Application\Service\Search\ZendSearch\QR\QrSearchQueryImpl;
use Procure\Application\Service\Search\ZendSearch\QR\QrSearchQueryImplFactory;
use Procure\Application\Service\Shared\SharedCmdServiceImpl;
use Procure\Application\Service\Shared\SharedCmdServiceImplFactory;
use Procure\Application\Service\Shared\SharedQueryServiceImpl;
use Procure\Application\Service\Shared\SharedQueryServiceImplFactory;
use Procure\Application\Service\Upload\PR\UploadPR;
use Procure\Application\Service\Upload\PR\UploadPRFactory;
use Procure\Infrastructure\Cache\CacheFactory;
use Procure\Infrastructure\Cache\RedisCacheFactory;
use Procure\Infrastructure\Doctrine\POCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\Factory\POCmdRepositoryFactory;
use Procure\Infrastructure\Doctrine\Factory\POQueryRepositoryFactory;
use Procure\Infrastructure\Logging\LoggerFactory;
use Procure\Infrastructure\Persistence\Doctrine\GrReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Doctrine\PoReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Doctrine\ProcureReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Doctrine\QrReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Doctrine\Factory\GrReporterRepositoryImplFactory;
use Procure\Infrastructure\Persistence\Doctrine\Factory\PoReporterRepositoryImplFactory;
use Procure\Infrastructure\Persistence\Doctrine\Factory\ProcureReporterRepositoryImplFactory;
use Procure\Infrastructure\Persistence\Doctrine\Factory\QrReporterRepositoryImplFactory;
use Procure\Infrastructure\Persistence\Reporting\Doctrine\PoApReportImpl;
use Procure\Infrastructure\Persistence\Reporting\Doctrine\PrGrReportImpl;
use Procure\Infrastructure\Persistence\Reporting\Doctrine\PrReportImplV1;
use Procure\Infrastructure\Persistence\Reporting\Doctrine\Factory\PoApReportImplFactory;
use Procure\Infrastructure\Persistence\Reporting\Doctrine\Factory\PrGrReportImplFactory;
use Procure\Infrastructure\Persistence\Reporting\Doctrine\Factory\PrReportImplFactory;

return array(
    'navigation' => array(
        'procure_navi' => array(
            array(
                'label' => 'Home',
                'route' => 'application',
                'icon' => 'glyphicon glyphicon-home'
            ),
            array(
                'label' => 'Add New PR',
                'route' => 'procure/default',
                'controller' => 'pr',
                'action' => 'create',
                'icon' => 'glyphicon glyphicon-plus'
            ),

            array(
                'label' => 'PR List',
                'route' => 'procure/default',
                'controller' => 'pr-report',
                'action' => 'header-status',
                'icon' => 'glyphicon glyphicon-list'
            ),


           /*  array(
                'label' => 'All PR',
                // 'uri' => "/procure/pr/all?pr_year=0&balance=0&is_active=1&sort_by=prNumber&sort=ASC&perPage=15",
                'route' => 'pr_status',
                'params' => array(
                    "status" => "all",
                    "row_number" => 2
                ),

                'icon' => 'glyphicon glyphicon-list'
            ),

            array(
                'label' => 'Pending PR',
                // 'uri' => "/procure/pr/all?pr_year=0&balance=0&is_active=1&sort_by=prNumber&sort=ASC&perPage=15",
                'route' => 'pr_status',
                'params' => array(
                    "status" => "pending",
                    "row_number" => 1
                ),

                'icon' => 'glyphicon glyphicon-list'
            ),

            array(
                'label' => 'Completed PR',
                // 'uri' => "/procure/pr/all?pr_year=0&balance=0&is_active=1&sort_by=prNumber&sort=ASC&perPage=15",
                'route' => 'pr_status',
                'params' => array(
                    "status" => "completed",
                    "row_number" => 1
                ),

                'icon' => 'glyphicon glyphicon-list'
            ),

            array(
                'label' => 'None-Row PR',
                // 'uri' => "/procure/pr/all?pr_year=0&balance=0&is_active=1&sort_by=prNumber&sort=ASC&perPage=15",
                'route' => 'pr_status',
                'params' => array(
                    "status" => "completed",
                    "row_number" => 0
                ),

                'icon' => 'glyphicon glyphicon-list'
            ), */

            array(
                'label' => 'Reporting',
                'route' => 'procure/default',
                'controller' => 'report',
                'action' => 'index',
                'icon' => 'fa fa-bar-chart'
            ),

            array(
                'label' => 'Log',
                'route' => 'procure/default',
                'controller' => 'activity-log',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-list'
            )
        )
    ),

    'router' => array(
        'routes' => array(

            'procure' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/procure',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Procure\Controller',
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

            'pr_status' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/procure/pr/all[/row_number=:row_number][/status=:status]',
                    'constraints' => array(
                        'status' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'row_number' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Procure\Controller\Pr',
                        'action' => 'all'
                    )
                )
            ),

            'completed_pr' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/procure/pr/all/[completed=:balance]',
                    'constraints' => array(
                        'balance' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Procure\Controller\Pr',
                        'action' => 'all'
                    )
                )
            ),

            'pr_rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/procure/pr-rest[/:id]',
                    'constraints' => array(
                        'id' => '[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'Procure\Controller\PrRest'
                    )
                )
            ),
            'pr_api' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/procure/pr-api[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Procure\API\PrController'
                    )
                )
            )
        )
    ),

    'console' => array(
        'router' => array(
            'routes' => array(
                'pr_console' => array(
                    'options' => array(
                        'route' => 'validate',
                        'defaults' => array(
                            'controller' => 'Procure\Controller\PrConsole',
                            'action' => 'validate'
                        )
                    )
                )
            )
        )
    ),

    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',

            'procure_navi' => 'Procure\Service\ProcureNavigationFactory', // <-- add this

            'Procure\Service\GrListener' => 'Procure\Service\GrListenereFactory',

            /*
             * |=============================
             * |Search Service.
             * |
             * |=============================
             */
            'Procure\Service\PrSearchService' => 'Procure\Service\PrSearchServiceFactory',
            'Procure\Service\QoSearchService' => 'Procure\Service\QoSearchServiceFactory',
            'Procure\Service\PoSearchService' => 'Procure\Service\PoSearchServiceFactory',
            'Procure\Service\GrSearchService' => 'Procure\Service\GrSearchServiceFactory',
            'Procure\Service\ApSearchService' => 'Procure\Service\ApSearchServiceFactory',
            
            
            /*
             * |=============================
             * |Application Service.
             * |
             * |=============================
             */
            
            // Old Service to delete.
            'Procure\Service\PrService' => 'Procure\Service\PrServiceFactory',
            'Procure\Service\QoService' => 'Procure\Service\QoServiceFactory',
            'Procure\Service\PoService' => 'Procure\Service\PoServiceFactory',
            'Procure\Service\GrService' => 'Procure\Service\GrServiceFactory',
            'Procure\Service\APInvoiceService' => 'Procure\Service\APInvoiceServiceFactory',
            'Procure\Service\ReService' => 'Procure\Service\ReServiceFactory',

            'Procure\Application\Service\PR\PRService' => PRServiceFactory::class,
            PRServiceV2::class => PRServiceFactoryV2::class,

            'Procure\Application\Service\PO\POService' => 'Procure\Application\Service\PO\POServiceFactory',
            'Procure\Application\Service\GR\GRService' => 'Procure\Application\Service\GR\GRServiceFactory',
            'Procure\Application\Service\AP\APService' => 'Procure\Application\Service\AP\APServiceFactory',
            QRService::class => QRServiceFactory::class,
            UploadPR::class => UploadPRFactory::class,

            'Procure\Service\Upload\PrRowUploadService' => 'Procure\Service\Upload\PrRowUploadServiceFactory',
            'Procure\Service\Upload\PrUploadService' => 'Procure\Service\Upload\PrUploadServiceFactory',

            /*
             * |=============================
             * |Application Service.
             * |
            // Repository Service
             * |=============================
             */
            'Procure\Infrastructure\Persistence\Doctrine\PRListRepository' => 'Procure\Infrastructure\Persistence\Doctrine\Factory\PRListRepositoryFactory',
            'Procure\Infrastructure\Persistence\Doctrine\POListRepository' => 'Procure\Infrastructure\Persistence\Doctrine\Factory\POListRepositoryFactory',
            'Procure\Infrastructure\Persistence\Doctrine\APRepoterRepositoryImpl' => 'Procure\Infrastructure\Persistence\Doctrine\Factory\APReporterRepositoryImplFactory',

            POCmdRepositoryImpl::class => POCmdRepositoryFactory::class,
            POQueryRepositoryImpl::class => POQueryRepositoryFactory::class,

            QrReportRepositoryImpl::class => QrReporterRepositoryImplFactory::class,
            GrReportRepositoryImpl::class => GrReporterRepositoryImplFactory::class,
            PrReportImplV1::class => PrReportImplFactory::class,
            PoReportRepositoryImpl::class => PoReporterRepositoryImplFactory::class,
            ProcureReportRepositoryImpl::class => ProcureReporterRepositoryImplFactory::class,
            PoApReportImpl::class => PoApReportImplFactory::class,
            PrGrReportImpl::class => PrGrReportImplFactory::class,

            // Shared Service
            SharedQueryServiceImpl::class => SharedQueryServiceImplFactory::class,
            SharedCmdServiceImpl::class => SharedCmdServiceImplFactory::class,

            // Reporing Service
            'Procure\Application\Reporting\PR\PrRowStatusReporter' => 'Procure\Application\Reporting\PR\PrRowStatusReporterFactory',
            'Procure\Application\Reporting\PO\PoReporter' => 'Procure\Application\Reporting\PO\PoReporterFactory',
            'Procure\Application\Reporting\AP\ApReporter' => 'Procure\Application\Reporting\AP\ApReporterFactory',
            QrReporter::class => QrReporterFactory::class,
            GrReporter::class => GrReporterFactory::class,
            PrReporter::class => PrReporterFactory::class,

            
            /*
             * |=============================
             * |Application Service.
             * |
             * |=============================
             */
         
            // Search Service
            'Procure\Application\Service\Search\ApSearchService' => 'Procure\Application\Service\Search\ApSearchServiceFactory',
            'Procure\Application\Service\Search\PoSearchService' => 'Procure\Application\Service\Search\PoSearchServiceFactory',
            'Procure\Application\Service\Search\PrSearchService' => 'Procure\Application\Service\Search\PrSearchServiceFactory',
            'Procure\Application\Service\Search\QrSearchService' => 'Procure\Application\Service\Search\QrSearchServiceFactory',

            // new
            PrSearchIndexImpl::class => PrSearchIndexImplFactory::class,
            PrSearchQueryImpl::class => PrSearchQueryImplFactory::class,

            QrSearchIndexImpl::class => QrSearchIndexImplFactory::class,
            QrSearchQueryImpl::class => QrSearchQueryImplFactory::class,

            // Event Handler
            'Procure\Application\Event\Handler\PoHeaderCreatedEventHandler' => 'Procure\Application\Event\Handler\PoHeaderCreatedEventHandlerFactory',

            /*
             * |=================================
             * | Event Bus
             * |
             * |==================================
             */
                        
            // Event Handler Resolver
            HandlerMapper::class => HandlerMapperFactory::class,
            EventBusService::class => EventBusServiceFactory::class,

            // Event Handler
            UpdateIndexOnApPosted::class => UpdateIndexOnApPostedFactory::class,
            UpdateIndexOnPoPosted::class => UpdateIndexOnPoPostedFactory::class,
            UpdateIndexOnQrPosted::class => UpdateIndexOnQrPostedFactory::class,

            UpdateIndexOnPrSubmitted::class => UpdateIndexOnPrSubmittedFactory::class,

            CreateGrOnApPosted::class => CreateGrOnApPostedFactory::class,
            CreateGrReversalOnApReversed::class => CreateGrReversalOnApReversedFactory::class,
            OnApPostedCreateGrByWarehouse::class => OnApPostedCreateGrByWarehouseFactory::class,
            OnApReversedCreateGrReversalByWarehouse::class => OnApReversedCreateGrReversalByWarehouseFactory::class,

            /*
             * |=================================
             * | Logger Service
             * |
             * |==================================
             */
            "ProcureLogger" => LoggerFactory::class,
            "ProcureCache" => CacheFactory::class,
            "ProcureRedisCache" => RedisCacheFactory::class
        )
    ),

    'controllers' => array(
        'factories' => array(
            'Procure\Controller\Index' => 'Procure\Controller\IndexControllerFactory',
            'Procure\Controller\PrSearch' => 'Procure\Controller\PrSearchControllerFactory',
            'Procure\Controller\PrRest' => 'Procure\Controller\PrRestControllerFactory',
            'Procure\Controller\PrConsole' => 'Procure\Controller\PrConsoleControllerFactory',
            'Procure\Controller\ChangeLog' => 'Procure\Controller\ChangeLogControllerFactory',
            'Procure\Controller\ActivityLog' => 'Procure\Controller\ActivityLogControllerFactory',

            'Procure\Controller\Pr' => 'Procure\Controller\PrControllerFactory',
            'Procure\Controller\PrRow' => 'Procure\Controller\PrRowControllerFactory',
            'Procure\Controller\PrAttachment' => 'Procure\Controller\PrAttachmentControllerFactory',
            'Procure\Controller\PrRowAttachment' => 'Procure\Controller\PrRowAttachmentControllerFactory',
            'Procure\Controller\PrSearch' => 'Procure\Controller\PrSearchControllerFactory',

            'Procure\Controller\Quote' => 'Procure\Controller\QuoteControllerFactory',
            'Procure\Controller\QuoteRow' => 'Procure\Controller\QuoteRowControllerFactory',
            'Procure\Controller\QuoteAttachment' => 'Procure\Controller\QuoteAttachmentControllerFactory',
            'Procure\Controller\QuoteSearch' => 'Procure\Controller\QuoteSearchControllerFactory',

            'Procure\Controller\Po' => 'Procure\Controller\PoControllerFactory',
            'Procure\Controller\PoRow' => 'Procure\Controller\PoRowControllerFactory',
            'Procure\Controller\PoAttachment' => 'Procure\Controller\PoAttachmentControllerFactory',
            'Procure\Controller\PoSearch' => 'Procure\Controller\PoSearchControllerFactory',

            'Procure\Controller\Gr' => 'Procure\Controller\GrControllerFactory',
            'Procure\Controller\GrRow' => 'Procure\Controller\GrRowControllerFactory',
            'Procure\Controller\GrAttachment' => 'Procure\Controller\GrAttachmentControllerFactory',
            'Procure\Controller\GrSearch' => 'Procure\Controller\GrSearchControllerFactory',

            'Procure\Controller\Return' => 'Procure\Controller\ReturnControllerFactory',
            // 'Procure\Controller\ReturnRow' => 'Procure\Controller\ReturnRowControllerFactory',
            'Procure\Controller\ReturnAttachment' => 'Procure\Controller\ReturnAttachmentControllerFactory',

            'Procure\Controller\PriceComparison' => 'Procure\Controller\PriceComparisonControllerFactory',

            'Procure\Controller\ApSearch' => 'Procure\Controller\ApSearchControllerFactory',
            'Procure\Controller\ApReport' => 'Procure\Controller\ApReportControllerFactory',
            'Procure\Controller\Ap' => 'Procure\Controller\ApControllerFactory',
            'Procure\Controller\Qr' => 'Procure\Controller\QrControllerFactory',
            'Procure\Controller\QrSearch' => 'Procure\Controller\QrSearchControllerFactory',

            'Procure\Controller\Report' => 'Procure\Controller\ReportControllerFactory',
            'Procure\Controller\PrReport' => 'Procure\Controller\PrReportControllerFactory',
            'Procure\Controller\PoReport' => 'Procure\Controller\PoReportControllerFactory',

            // API
            'Procure\API\PrController' => 'Procure\API\PrControllerFactory'
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'Procure/layout-fluid-1' => __DIR__ . '/../view/layout/layout-fluid-1.phtml',

            'Procure/layout-fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml',
            'Procure/layout-fullscreen' => __DIR__ . '/../view/layout/layout-fullscreen.phtml',
            'procure/index/index' => __DIR__ . '/../view/procure/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'layout/fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        )
    )
);
