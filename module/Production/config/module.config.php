<?php
return array(
    'navigation' => array(
        'production_navi' => array(
            array(
                'label' => 'Home',
                'route' => 'application'
            ),
        array (
        'label' => 'Maintenance & Repair',
        'route' => 'production/default',
          'controller' => 'maintenance',
          'action' => 'add',
          'icon' => 'glyphicon glyphicon-plus'
          ),
            
            array (
                'label' => 'IE Project',
                'route' => 'production/default',
                'controller' => 'maintenance',
                'action' => 'add',
                'icon' => 'glyphicon glyphicon-plus'
            ),
                 )
    ),
    'router' => array(
        'routes' => array(

            'production' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/production',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Production\Controller',
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
            'production_navi' => 'Production\Service\ProductionNavigationFactory' // <-- add this
        )
    ),
    'controllers' => array(
        'factories' => array(
            'Production\Controller\Index' => 'Production\Controller\IndexControllerFactory'
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
            'Production/layout-fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml',
            'user/index/index' => __DIR__ . '/../view/user/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'layout/fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    )
);
