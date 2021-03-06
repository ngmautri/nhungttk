<?php

/**
 * Configuration Module: User
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
namespace User;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Session\Container;
use Zend\Uri\Exception\InvalidUriException;
use User\Model\User;
use User\Model\UserTable;
use User\Model\AclResource;
use User\Model\AclResourceTable;
use User\Model\AclRole;
use User\Model\AclRoleTable;
use User\Model\AclRoleResource;
use User\Model\AclRoleResourceTable;
use User\Model\AclWhiteList;
use User\Model\AclWhiteListTable;
use User\Model\AclUserRole;
use User\Model\AclUserRoleTable;
use Zend\Permissions\Acl;
use Zend\Navigation\Page\Mvc as MvcPage;
use Zend\Navigation\AbstractContainer;

class Module
{

    /*
     * The onBootstrap() method is called for every module implementing this feature, on every page request,
     * and should only be used for performing lightweight tasks such as registering event listeners.
     */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_ROUTE, array(
            $this,
            'checkIdentity'
        ), - 100);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array(
            $this,
            'checkACL'
        ), 100);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    /**
     *
     * @param MvcEvent $e
     * @return void|\Zend\Stdlib\ResponseInterface
     */
    function checkACL(MvcEvent $e)
    {
        $match = $e->getRouteMatch();
        $app = $e->getApplication();
        $sm = $app->getServiceManager();
        $viewModel = $app->getMvcEvent()->getViewModel();

        // $request = $sm->get('Request');
        // var_dump($request->isXmlHttpRequest());

        // $controller = $e->getTarget ();
        // $layout = $e->getRouteMatch ()->getParams();
        // var_dump($layout);

        $controller = $e->getRouteMatch()->getParam('controller');
        $action = $e->getRouteMatch()->getParam('action');
        $action = str_replace("-", "", $action);
        $action = str_replace("_", "", $action);
        $requestedResourse = strtoupper($controller . "-" . $action = str_replace("-", "", $action));

        $session = new Container('MLA_USER');
        $hasUser = $session->offsetExists('user');
        $hasACL = $session->offsetExists('ACL');
        $hasPRCart = $session->offsetExists('cart_items');

        // Route is whitelisted
        $name = $match->getMatchedRouteName();
        // var_dump($name);

        if (in_array($name, array(
            'login',
            'logout',
            'user_register',
            'test_console',
            'user_register_confirmation',
            'access_denied',
            'not_found',
            'order_suggestion_console',
            'pr_rest',
            'pr_console',
            'send_to_rabitmq',
            'receive_from_rabitmq',
            'item-api',
            'warehouse-api',
            'inventory-trx-api',
            'pr_api',
            
        ))) {
            return;
        }

        if ($hasUser) {

            $user = $session->offsetGet('user');
            $viewModel->user = $user['firstname'] . ' ' . $user['lastname'];
            $user_id = $user['id'];

            if ($hasPRCart) {
                $viewModel->cart_items = $session->offsetGet('cart_items');
            } else {

                $cartItemTable = $sm->get('Procurement\Model\PurchaseRequestCartItemTable');
                $total_cart_items = $cartItemTable->getTotalCartItems($user_id);
                $session->offsetSet('cart_items', $total_cart_items);
                $viewModel->cart_items = $total_cart_items;
            }

            if (! $hasACL) {
                // get ACL
                $acl = $sm->get('Application\Service\AclService');
                $acl = $acl->initAcl();
                $session->offsetSet('ACL', $acl);
            } else {

                $acl = $session->offsetGet('ACL');
            }

            $hasRoles = $session->offsetExists('roles');

            if (! $hasRoles) {
                $aclUserRole = $sm->get('User\Model\UserTable');
                $roles = $aclUserRole->getRoleByUserId($user_id);
                $session->offsetSet('roles', $roles);
            } else {
                $roles = $session->offsetGet('roles');
                // var_dump ( $roles );
            }

            /*
             * $aclUserRole = $sm->get ( 'User\Model\AclUserRoleTable' );
             * $roles = $aclUserRole->getRoleByUserId ( $user_id );
             */

            $isAllowedAccess = false;
            $viewModel->isAdmin = false;
            $viewModel->isProcurement = false;

            if (count($roles) > 0) {

                foreach ($roles as $role) {
                    $isAllowed = $acl->isAccessAllowed($role['role'], $requestedResourse, null);
                    // var_dump($requestedResourse);
                    // var_dump($role ['role']);

                    if ($isAllowed) {
                        $isAllowedAccess = true;

                        if (strtoupper($role['role']) == 'ADMINISTRATOR') {
                            $viewModel->isAdmin = true;
                        }

                        if (strtoupper($role['role']) == 'PROCUREMENT-MEMBER') {
                            $viewModel->isProcurement = true;
                        }

                        // break;
                        // var_dump
                    }
                }
            } else {
                // member only
                $isAllowed = $acl->isAccessAllowed('member', $requestedResourse, null);
                if ($isAllowed) {
                    $isAllowedAccess = true;
                }
            }

            // var_dump($user);

            if ($isAllowedAccess === false) {
                // die('<h3>Permission denied</h3>' . $requestedResourse);

                // Redirect to the user login page, as an example

                $router = $e->getRouter();
                $url = $router->assemble(array(), array(
                    'name' => 'access_denied'
                ));

                $response = $e->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);

                return $response;
            }
        } else {
            // Redirect to the user login page, as an example

            $router = $e->getRouter();
            $url = $router->assemble(array(), array(
                'name' => 'login'
            ));

            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;
        }
    }

    /**
     *
     * @param MvcEvent $e
     * @return void|\Zend\Stdlib\ResponseInterface
     */
    function checkIdentity(MvcEvent $e)
    {
        $request = $e->getRequest();
        $response = $e->getResponse();
        $target = $e->getTarget();

        $match = $e->getRouteMatch();
        $app = $e->getApplication();
        $sm = $app->getServiceManager();

        // Route is whitelisted
        $name = $match->getMatchedRouteName();
        if (in_array($name, array(
            'login',
            'logout',
            'user_register',
            'test_console',
            'user_register_confirmation',
            'access_denied',
            'order_suggestion_console',
            'pr_rest',
            'pr_console',
            'send_to_rabitmq',
            'receive_from_rabitmq',
            'item-api',
            'warehouse-api',
            'inventory-trx-api',
            'pr_api',
        ))) {
            return;
        }

        $requestUri = null;
        if ($request !== null) {
            $requestUri = $request->getRequestUri();
        }

        $auth = $sm->get('AuthService');

        // No route match, this is a 404
        /*
         * if (! $match instanceof RouteMatch) {
         * return;
         * }
         */

        // User is authenticated
        if ($auth->hasIdentity()) {

            $session = new Container('MLA_USER');
            $hasUser = $session->offsetExists('user');
            return;
        }

        // Redirect to the user login page, as an example
        $router = $e->getRouter();
        /*
         * $url = $router->assemble ( array (), array (
         * 'name' => 'login'
         * ) );
         */

        $router = $e->getRouter();
        $url = $router->assemble(array(), array(
            'name' => 'login'
        ));
        // $url = "http://localhost:81/user/auth/authenticate?redirect=" . $requestUri;
        // echo $url;
        // $response->setHeaders ( $response->getHeaders ()->addHeaderLine ( 'Location', $url ) );

        // $response = $e->getResponse ();
        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);

        return $response;
    }

    // Add this method:
    public function getServiceConfig()
    {
        return array(
            'factories' => array(

                'User\Model\UserTable' => function ($sm) {
                    $tableGateway = $sm->get('UserTableGateway');
                    $table = new UserTable($tableGateway);
                    return $table;
                },

                'UserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('mla_users', $dbAdapter, null, $resultSetPrototype);
                },

                // Acl Resource
                'User\Model\AclResourceTable' => function ($sm) {
                    $tableGateway = $sm->get('AclResourceTableGateway');
                    $table = new AclResourceTable($tableGateway);
                    return $table;
                },

                'AclResourceTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AclResource());
                    return new TableGateway('mla_acl_resources', $dbAdapter, null, $resultSetPrototype);
                },

                // Acl Role
                'User\Model\AclRoleTable' => function ($sm) {
                    $tableGateway = $sm->get('AclRoleTableGateway');
                    $table = new AclRoleTable($tableGateway);
                    return $table;
                },

                'AclRoleTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AclRole());
                    return new TableGateway('mla_acl_roles', $dbAdapter, null, $resultSetPrototype);
                },

                // Acl Role
                'User\Model\AclRoleResourceTable' => function ($sm) {
                    $tableGateway = $sm->get('AclRoleResourceTableGateway');
                    $table = new AclRoleResourceTable($tableGateway);
                    return $table;
                },

                'AclRoleResourceTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AclRoleResource());
                    return new TableGateway('mla_acl_role_resource', $dbAdapter, null, $resultSetPrototype);
                },

                // Acl White List
                'User\Model\AclWhiteListTable' => function ($sm) {
                    $tableGateway = $sm->get('AclWhiteListTableGateway');
                    $table = new AclWhiteListTable($tableGateway);
                    return $table;
                },

                'AclWhiteListTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AclWhiteList());
                    return new TableGateway('mla_acl_whitelist', $dbAdapter, null, $resultSetPrototype);
                },

                // Acl User Rolse
                'User\Model\AclUserRoleTable' => function ($sm) {
                    $tableGateway = $sm->get('AclUserRoleTable');
                    $table = new AclUserRoleTable($tableGateway);
							return $table;
						},
						
						'AclUserRoleTable' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new AclUserRole () );
							return new TableGateway ( 'mla_acl_user_role', $dbAdapter, null, $resultSetPrototype );
						},
						
						'User\Service\RegisterService' => 'User\Service\RegisterServiceFactory',
						'User\Listener\RegisterListener' => 'User\Listener\RegisterListenerFactory',
						'User\Service\Acl' => 'User\Service\AclFactory',
						'User\Service\ArticleCategory' => 'User\Service\ArticleCategoryFactory'
				) 
		);
	}
}
