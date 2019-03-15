<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            
            'user' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/user',
                    'defaults' => array(
                        '__NAMESPACE__' => 'User\Controller',
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
            
            'login' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/auth/authenticate',
                    'defaults' => array(
                        'module' => 'User',
                        'controller' => 'User\Controller\Auth',
                        'action' => 'authenticate'
                    )
                )
            ),
            
            'logout' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/auth/logout',
                    'defaults' => array(
                        'module' => 'User',
                        'controller' => 'User\Controller\Auth',
                        'action' => 'logout'
                    )
                )
            ),
            
            'user_register' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/index/register',
                    'defaults' => array(
                        'module' => 'User',
                        'controller' => 'User\Controller\Index',
                        'action' => 'register'
                    )
                )
            ),
            
            'access_denied' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/index/deny',
                    'defaults' => array(
                        'module' => 'User',
                        'controller' => 'User\Controller\Index',
                        'action' => 'deny'
                    )
                )
            ),
            
            'user_register_confirmation' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user/index/confirm',
                    'defaults' => array(
                        'module' => 'User',
                        'controller' => 'User\Controller\Index',
                        'action' => 'confirm'
                    )
                )
            )
        
        )
    
    ),
    
    'console' => array(
        'router' => array(
            'routes' => array(
                
                'test_console' => array(
                    'options' => array(
                        'route' => 'users',
                        'defaults' => array(
                            'controller' => 'User\Controller\Index',
                            'action' => 'console'
                        )
                    )
                )
            )
        )
    ),
    
    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'User\Service\UserService' => 'User\Service\UserServiceFactory'
            
        )
    ),
   
    'controllers' => array(
        'invokables' => array(
            'User\Controller\Index' => 'User\Controller\IndexController',
            'User\Controller\Admin' => 'User\Controller\AdminController'
        ),
        'factories' => array(
            'User\Controller\Role' => 'User\Controller\RoleControllerFactory',
            'User\Controller\Auth' => 'User\Controller\AuthControllerFactory',
            'User\Controller\Profile' => 'User\Controller\ProfileControllerFactory',
            'User\Controller\Inbox' => 'User\Controller\InboxControllerFactory',
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
            'User/login' => __DIR__ . '/../view/layout/login.phtml',
            
            'User/layout-fluid' => __DIR__ . '/../view/layout/layout-fluid.phtml',
            
            'layout/user/ajax' => __DIR__ . '/../view/layout/ajax.phtml',
            'layout/user/login' => __DIR__ . '/../view/layout/layout-login.phtml',
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
        'invokables' => array(
            'UserPlugin' => 'User\Controller\Plugin\UserPlugin'
        )
    )
);
