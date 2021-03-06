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
        'wf_navi' => array(
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
            )
        
        )
    ),
    
    'router' => array(
        'routes' => array(
            
            'workflow' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/workflow',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Workflow\Controller',
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
            'Workflow\Service\WorkflowService' => 'Workflow\Service\WorkflowServiceFactory',
            'wf_navi' => 'Workflow\Service\WfNavigationFactory' // <-- add this
        
        )
    ),
   
   'controllers' => array(
        'factories' => array(
            'Workflow\Controller\WF' => 'Workflow\Controller\WFControllerFactory',
            'Workflow\Controller\Transition' => 'Workflow\Controller\TransitionControllerFactory',
            'Workflow\Controller\WorkItem' => 'Workflow\Controller\WorkItemControllerFactory',            
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
            'Workflow/layout-fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml',
            
            'user/index/index' => __DIR__ . '/../view/user/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'layout/fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    ),
    
    // Plugin
    'controller_plugins' => array(
        'factories' => array(
            'WfPlugin' => 'Workflow\Controller\Plugin\WfPluginFactory'
        )
    )
);
