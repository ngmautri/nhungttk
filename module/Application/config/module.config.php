<?php
use Application\Controller\MessageStoreController;
use Application\Controller\MessageStoreControllerFactory;
use Application\Infrastructure\Doctrine\MessageStoreRepository;
use Application\Infrastructure\Doctrine\Factory\MessageStoreRepositoryFactory;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
return array(

    'caches' => array(

        'FileSystemCache' => array(

            'adapter' => array(
                'name' => 'filesystem'
            ),

            'options' => array(

                'cache_dir' => './data/cache/',
                'ttl' => 3600

                // other options
            ),
            'plugins' => array(
                array(
                    'name' => 'serializer',
                    'options' => array()
                ),
                'exception_handler' => array(
                    'throw_exceptions' => true
                )
            )
        )

        /*
     * 'memcached' => array( // can be called directly via SM in the name of 'memcached'
     * 'adapter' => array(
     * 'name' => 'memcached',
     * 'options' => array(
     * 'ttl' => 7200,
     * 'servers' => array(
     * array(
     * '127.0.0.1',
     * 11211
     * )
     * ),
     * 'namespace' => 'MYMEMCACHEDNAMESPACE',
     * 'liboptions' => array(
     * 'COMPRESSION' => true,
     * 'binary_protocol' => true,
     * 'no_block' => true,
     * 'connect_timeout' => 100
     * )
     * )
     * ),
     * 'plugins' => array(
     * 'exception_handler' => array(
     * 'throw_exceptions' => false
     * )
     * )
     * )
     */
    ),

    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Home',
                'route' => 'application'
            ),
            array(
                'label' => 'Company',
                'route' => 'application/default',
                'controller' => 'company',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),
            array(
                'label' => 'Department',
                'route' => 'application/default',
                'controller' => 'department',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),
            array(
                'label' => 'Country',
                'route' => 'application/default',
                'controller' => 'country',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),
            array(
                'label' => 'Currency',
                'route' => 'application/default',
                'controller' => 'currency',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),

            array(
                'label' => 'Incoterms',
                'route' => 'application/default',
                'controller' => 'incoterm',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),

            array(
                'label' => 'Payment Terms',
                'route' => 'application/default',
                'controller' => 'payment-term',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),
            array(
                'label' => 'Payment Method',
                'route' => 'application/default',
                'controller' => 'pmt-method',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),

            array(
                'label' => 'Posting Period',
                'route' => 'finance/default',
                'controller' => 'posting-period',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),
            array(
                'label' => 'Document Number',
                'route' => 'application/default',
                'controller' => 'doc-number',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),

            array(
                'label' => 'Unit Of Measures',
                'route' => 'application/default',
                'controller' => 'uom',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),
            array(
                'label' => 'Item Category',
                'route' => 'application/default',
                'controller' => 'item-category',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),

            array(
                'label' => 'ACL',
                'route' => 'application/default',
                'controller' => 'acl',
                'action' => 'list-resources',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),

            array(
                'label' => 'ACL Role',
                'route' => 'application/default',
                'controller' => 'role',
                'action' => 'list',
                'icon' => 'glyphicon glyphicon-triangle-right'
            ),
            array(
                'label' => 'System Information',
                'route' => 'application/default',
                'controller' => 'index',
                'action' => 'info',
                'icon' => 'glyphicon glyphicon-triangle-right'
            )
        )
    ),

    'doctrine' => array(
        'driver' => array(
            'Application_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/Application/Entity'
                )
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Application\Entity' => 'Application_driver'
                )
            )
        )
        /*
     * 'configuration' => array(
     * 'orm_default' => array(
     * 'metadata_cache' => 'filesystem',
     * 'query_cache' => 'filesystem',
     * 'result_cache' => 'filesystem',
     * 'generate_proxies' => true,
     * )
     * )
     */
    ),

    'router' => array(
        'routes' => array(

            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
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
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'locale' => '[a-zA-Z]{2}_[a-zA-Z]{2}'
                            ),
                            'defaults' => array(
                                'locale' => 'de_DE'
                            )
                        )
                    )
                )
            )
        )
    ),

    'console' => array(
        'router' => array(
            'routes' => array(
                'send_to_rabitmq' => array(
                    'options' => array(
                        'route' => 'send_to_rabitmq',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Console',
                            'action' => 'send_to_rabitmq'
                        )
                    )
                ),
                'receive_from_rabitmq' => array(
                    'options' => array(
                        'route' => 'receive_from_rabitmq',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Console',
                            'action' => 'receive_msg'
                        )
                    )
                )
            )
        )
    ),

    'service_manager' => array(
        'aliases' => array(
            // important for Internationation
            'translator' => 'mvctranslator'
        ),

        'factories' => array(

            // Listner
            'Application\Listener\PictureUploadListener' => 'Application\Listener\PictureUploadListenerFactory',
            'Application\Listener\LoggingListener' => 'Application\Listener\LoggingListenerFactory',
            'Application\Listener\ViewListener' => 'Application\Listener\ViewListenerFactory',
            'Application\Listener\FinanceListener' => 'Application\Listener\FinanceListenerFactory',
            'Application\Listener\ProcureListener' => 'Application\Listener\ProcureListenerFactory',

            'Application\Service\SmtpOutlookService' => 'Application\Service\SmtpOutlookServiceFactory',
            'Application\Service\SmtpWebDeService' => 'Application\Service\SmtpWebDeServiceFactory',

            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory', // <-- add this

            'Application\Service\ApplicationService' => 'Application\Service\ApplicationServiceFactory',
            'Application\Service\PdfService' => 'Application\Service\PdfServiceFactory',
            'Application\Service\ExcelService' => 'Application\Service\ExcelServiceFactory',
            'Application\Service\AclService' => 'Application\Service\AclServiceFactory',
            'Application\Service\DepartmentService' => 'Application\Service\DepartmentServiceFactory',
            'Application\Service\ItemCategoryService' => 'Application\Service\ItemCategoryServiceFactory',
            'Application\Service\AppSearchService' => 'Application\Service\AppSearchServiceFactory',
            'Application\Service\AttachmentService' => 'Application\Service\AttachmentServiceFactory',

            'Application\Service\IncotermService' => 'Application\Service\IncotermServiceFactory',
            'Application\Service\PaymentTermService' => 'Application\Service\PaymentTermServiceFactory',
            'Application\Service\PmtMethodService' => 'Application\Service\PmtMethodServiceFactory',
            \Application\Application\Service\MessageStore\MessageQuery::class => \Application\Application\Service\MessageStore\MessageQueryFactory::class,

            // Report
            MessageStoreRepository::class => MessageStoreRepositoryFactory::class
        )
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo'
            )
        )
    ),

    // Plugin
    'controller_plugins' => array(
        'factories' => array(
            'AttachmentPlugin' => 'Application\Controller\Plugin\AttachmentPluginFactory',
            'Nmtplugin' => 'Application\Controller\Plugin\NmtPluginFactory'
        )
    ),

    'controllers' => array(
        /**
         *
         * @deprecated in zf3
         */
        'invokables' => array(),

        'factories' => array(
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
            'Application\Controller\Company' => 'Application\Controller\CompanyControllerFactory',
            'Application\Controller\DocNumber' => 'Application\Controller\DocNumberControllerFactory',
            'Application\Controller\Backup' => 'Application\Controller\BackupControllerFactory',
            'Application\Controller\User' => 'Application\Controller\UserControllerFactory',
            'Application\Controller\SearchIndex' => 'Application\Controller\SearchIndexControllerFactory',
            'Application\Controller\QrCode' => 'Application\Controller\QrCodeControllerFactory',
            'Application\Controller\Locale' => 'Application\Controller\LocaleControllerFactory',
            'Application\Controller\Cache' => 'Application\Controller\CacheControllerFactory',

            'Application\Controller\Incoterm' => 'Application\Controller\IncotermControllerFactory',
            'Application\Controller\PaymentTerm' => 'Application\Controller\PaymentTermControllerFactory',
            'Application\Controller\Console' => 'Application\Controller\ConsoleControllerFactory',
            'Application\Controller\MessageStore' => 'Application\Controller\MessageStoreControllerFactory'
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
            'Application/layout-fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml',

            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    )
);
