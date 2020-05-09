<?php

/**
 * Configuration file.
 * @author Ngyuyen Mau Tri
 */
use Procure\Application\EventBus\Handler\AP\UpdateIndexOnApPosted;
use Procure\Application\EventBus\Handler\AP\UpdateIndexOnApPostedFactory;
use Procure\Application\EventBus\Handler\GR\GrOnApPostedHandler;
use Procure\Application\EventBus\Handler\GR\GrOnApPostedHandlerFactory;
use Procure\Application\EventBus\Handler\PO\UpdateIndexOnPoPosted;
use Procure\Application\EventBus\Handler\PO\UpdateIndexOnPoPostedFactory;
use Procure\Application\EventBus\Handler\PR\UpdateIndexOnPrSubmitted;
use Procure\Application\EventBus\Handler\PR\UpdateIndexOnPrSubmittedFactory;
use Procure\Application\Eventbus\EventBusService;
use Procure\Application\Eventbus\EventBusServiceFactory;
use Procure\Application\Eventbus\HandlerMapper;
use Procure\Application\Eventbus\HandlerMapperFactory;
use Procure\Application\Reporting\GR\GrReporter;
use Procure\Application\Reporting\GR\GrReporterFactory;
use Procure\Application\Reporting\PR\PrReporter;
use Procure\Application\Reporting\PR\PrReporterFactory;
use Procure\Application\Reporting\QR\QrReporter;
use Procure\Application\Reporting\QR\QrReporterFactory;
use Procure\Application\Service\QR\QRService;
use Procure\Application\Service\QR\QRServiceFactory;
use Procure\Infrastructure\Persistence\Doctrine\GrReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Doctrine\PoReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Doctrine\PrReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Doctrine\ProcureReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Doctrine\QrReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Doctrine\Factory\GrReporterRepositoryImplFactory;
use Procure\Infrastructure\Persistence\Doctrine\Factory\PoReporterRepositoryImplFactory;
use Procure\Infrastructure\Persistence\Doctrine\Factory\PrReporterRepositoryImplFactory;
use Procure\Infrastructure\Persistence\Doctrine\Factory\ProcureReporterRepositoryImplFactory;
use Procure\Infrastructure\Persistence\Doctrine\Factory\QrReporterRepositoryImplFactory;

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
                'label' => 'PR Row Report',
                'route' => 'procure/default',
                'controller' => 'pr-report',
                'action' => 'row-status',
                'icon' => 'glyphicon glyphicon-list'
            ),

            array(
                'label' => 'PO Row Report',
                'route' => 'procure/default',
                'controller' => 'po-report',
                'action' => 'row-status',
                'icon' => 'glyphicon glyphicon-list'
            ),

            array(
                'label' => 'AP Row Report',
                'route' => 'procure/default',
                'controller' => 'ap-report',
                'action' => 'row-status',
                'icon' => 'glyphicon glyphicon-list'
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
                        'id' => '[a-zA-Z][a-zA-Z0-9_-]*'
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

            'Procure\Service\PrService' => 'Procure\Service\PrServiceFactory',
            'Procure\Service\QoService' => 'Procure\Service\QoServiceFactory',
            'Procure\Service\PoService' => 'Procure\Service\PoServiceFactory',
            'Procure\Service\GrService' => 'Procure\Service\GrServiceFactory',
            'Procure\Service\APInvoiceService' => 'Procure\Service\APInvoiceServiceFactory',
            'Procure\Service\ReService' => 'Procure\Service\ReServiceFactory',

            'Procure\Service\PrSearchService' => 'Procure\Service\PrSearchServiceFactory',
            'Procure\Service\QoSearchService' => 'Procure\Service\QoSearchServiceFactory',
            'Procure\Service\PoSearchService' => 'Procure\Service\PoSearchServiceFactory',
            'Procure\Service\GrSearchService' => 'Procure\Service\GrSearchServiceFactory',
            'Procure\Service\ApSearchService' => 'Procure\Service\ApSearchServiceFactory',

            'Procure\Service\Upload\PrRowUploadService' => 'Procure\Service\Upload\PrRowUploadServiceFactory',
            'Procure\Service\Upload\PrUploadService' => 'Procure\Service\Upload\PrUploadServiceFactory',

            // Repository Service
            'Procure\Infrastructure\Persistence\Doctrine\PRListRepository' => 'Procure\Infrastructure\Persistence\Doctrine\Factory\PRListRepositoryFactory',
            'Procure\Infrastructure\Persistence\Doctrine\POListRepository' => 'Procure\Infrastructure\Persistence\Doctrine\Factory\POListRepositoryFactory',
            'Procure\Infrastructure\Persistence\Doctrine\APRepoterRepositoryImpl' => 'Procure\Infrastructure\Persistence\Doctrine\Factory\APReporterRepositoryImplFactory',

            'Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository' => 'Procure\Infrastructure\Doctrine\Factory\POCmdRepositoryFactory',
            'Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository' => 'Procure\Infrastructure\Doctrine\Factory\POQueryRepositoryFactory',
            QrReportRepositoryImpl::class => QrReporterRepositoryImplFactory::class,
            GrReportRepositoryImpl::class => GrReporterRepositoryImplFactory::class,
            PrReportRepositoryImpl::class => PrReporterRepositoryImplFactory::class,
            PoReportRepositoryImpl::class => PoReporterRepositoryImplFactory::class,
            ProcureReportRepositoryImpl::class => ProcureReporterRepositoryImplFactory::class,

            // Reporing Service
            'Procure\Application\Reporting\PR\PrRowStatusReporter' => 'Procure\Application\Reporting\PR\PrRowStatusReporterFactory',
            'Procure\Application\Reporting\PO\PoReporter' => 'Procure\Application\Reporting\PO\PoReporterFactory',
            'Procure\Application\Reporting\AP\ApReporter' => 'Procure\Application\Reporting\AP\ApReporterFactory',
            QrReporter::class => QrReporterFactory::class,
            GrReporter::class => GrReporterFactory::class,
            PrReporter::class => PrReporterFactory::class,

            // Appliation Service
            'Procure\Application\Service\PR\PRService' => 'Procure\Application\Service\PR\PRServiceFactory',
            'Procure\Application\Service\PO\POService' => 'Procure\Application\Service\PO\POServiceFactory',
            'Procure\Application\Service\GR\GRService' => 'Procure\Application\Service\GR\GRServiceFactory',
            'Procure\Application\Service\AP\APService' => 'Procure\Application\Service\AP\APServiceFactory',
            QRService::class => QRServiceFactory::class,

            // Search Service
            'Procure\Application\Service\Search\ApSearchService' => 'Procure\Application\Service\Search\ApSearchServiceFactory',
            'Procure\Application\Service\Search\PoSearchService' => 'Procure\Application\Service\Search\PoSearchServiceFactory',
            'Procure\Application\Service\Search\PrSearchService' => 'Procure\Application\Service\Search\PrSearchServiceFactory',
            'Procure\Application\Service\Search\QrSearchService' => 'Procure\Application\Service\Search\QrSearchServiceFactory',

            // Event Handler
            'Procure\Application\Event\Handler\PoHeaderCreatedEventHandler' => 'Procure\Application\Event\Handler\PoHeaderCreatedEventHandlerFactory',

            // Event Handler Resolver
            HandlerMapper::class => HandlerMapperFactory::class,
            EventBusService::class => EventBusServiceFactory::class,

            // Event Handler
            UpdateIndexOnApPosted::class => UpdateIndexOnApPostedFactory::class,
            UpdateIndexOnPoPosted::class => UpdateIndexOnPoPostedFactory::class,
            UpdateIndexOnPrSubmitted::class => UpdateIndexOnPrSubmittedFactory::class,
            GrOnApPostedHandler::class => GrOnApPostedHandlerFactory::class
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
            'Procure\Controller\ReturnRow' => 'Procure\Controller\ReturnRowControllerFactory',
            'Procure\Controller\ReturnAttachment' => 'Procure\Controller\ReturnAttachmentControllerFactory',

            'Procure\Controller\PriceComparison' => 'Procure\Controller\PriceComparisonControllerFactory',

            'Procure\Controller\ApSearch' => 'Procure\Controller\ApSearchControllerFactory',
            'Procure\Controller\ApReport' => 'Procure\Controller\ApReportControllerFactory',
            'Procure\Controller\Ap' => 'Procure\Controller\ApControllerFactory',
            'Procure\Controller\Qr' => 'Procure\Controller\QrControllerFactory',
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
        )
    )
);
