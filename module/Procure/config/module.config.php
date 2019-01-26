<?php
/**
 * 
 */
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
                'action' => 'add',
                'icon' => 'glyphicon glyphicon-plus'
            ),
            
          /*   array(
                'label' => 'PR List',
                'route' => 'procure/default',
                'controller' => 'pr',
                'action' => 'all',
                'icon' => 'glyphicon glyphicon-list'
            ), */
            
          
            array(
                'label' => 'All PR',
                //'uri'  => "/procure/pr/all?pr_year=0&balance=0&is_active=1&sort_by=prNumber&sort=ASC&perPage=15",
                'route' => 'pr_status',
                'params'=>array(
                    "status"=>"all",
                    "row_number"=> 2,
                ),
                
                'icon' => 'glyphicon glyphicon-list',
                
            ),
            
            
            array(
                'label' => 'Pending PR',
                //'uri'  => "/procure/pr/all?pr_year=0&balance=0&is_active=1&sort_by=prNumber&sort=ASC&perPage=15",
                'route' => 'pr_status',
                'params'=>array(
                    "status"=>"pending",
                    "row_number"=> 1,
                ),
                
                'icon' => 'glyphicon glyphicon-list',
                
            ),
            
            array(
                'label' => 'Completed PR',
                //'uri'  => "/procure/pr/all?pr_year=0&balance=0&is_active=1&sort_by=prNumber&sort=ASC&perPage=15",
                'route' => 'pr_status',
                 'params'=>array(
                    "status"=>"completed",
                     "row_number"=> 1,
                ), 
                
                'icon' => 'glyphicon glyphicon-list',
                
            ),
            
            array(
                'label' => 'None-Row PR',
                //'uri'  => "/procure/pr/all?pr_year=0&balance=0&is_active=1&sort_by=prNumber&sort=ASC&perPage=15",
                'route' => 'pr_status',
                'params'=>array(
                    "status"=>"completed",
                    "row_number"=> 0,
                ),
                
                'icon' => 'glyphicon glyphicon-list',
                
            ),
            
            array(
                'label' => 'PR Row',
                'route' => 'procure/default',
                'controller' => 'pr-row',
                'action' => 'all',
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
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/procure/pr/all[/row_number=:row_number][/status=:status]',
                    'constraints' => array(
                        'status'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'row_number'     => '[0-9]+',
                        
                    ),
                    'defaults' => array(
                        'controller' => 'Procure\Controller\Pr',
                        'action' => 'all',
                    ),
                ),
            ),
            
            'completed_pr' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/procure/pr/all/[completed=:balance]',
                    'constraints' => array(
                        'balance'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Procure\Controller\Pr',
                        'action' => 'all',
                    ),
                ),
            ),
                        
            'pr_rest' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/procure/pr-rest[/:id]',
                    'constraints' => array(
                        'id'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Procure\Controller\PrRest',
                    ),
                ),
            ),
        
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
            
            'Procure\Controller\ApSearch' => 'Procure\Controller\ApSearchControllerFactory',
            
            'Procure\Controller\Return' => 'Procure\Controller\ReturnControllerFactory',
            'Procure\Controller\ReturnRow' => 'Procure\Controller\ReturnRowControllerFactory',
            'Procure\Controller\ReturnAttachment' => 'Procure\Controller\ReturnAttachmentControllerFactory',
     
            'Procure\Controller\PriceComparison' => 'Procure\Controller\PriceComparisonControllerFactory',
            
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
    ),
    
 
);
