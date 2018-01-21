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
        'calendar_navi' => array(
            array(
                'label' => 'Home',
                'route' => 'application',
                'icon' => 'glyphicon glyphicon-home'
            ),
            array(
                'label' => 'Month',
                'route' => 'calendar/default',
                'controller' => 'index',
                'action' => 'month',
                'icon' => 'glyphicon glyphicon-list'
            ),
        )
    ),
    
    'router' => array(
        'routes' => array(
            
            'calendar' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/calendar',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Calendar\Controller',
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
            'calendar_navi' => 'Calendar\Service\CalendarNavigationFactory', // <-- add this
            'Calendar\Service\CalendarService' => 'Calendar\Service\CalendarServiceFactory',
            
            
            
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
    'controllers' => array(
        'factories' => array(
            'Calendar\Controller\Index' => 'Calendar\Controller\IndexControllerFactory',
         )
    
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'Calendar/layout-fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml',
            'Calendar/index/index' => __DIR__ . '/../view/procure/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'layout/fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        )
    )

);
