<?php
namespace Application;

use Application\Model\AclRole;
use Application\Model\AclRoleTable;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter as DbTableAuthAdapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

/**
 * Update
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Module
{

    /*
     * |--------------------------------------------------------------------------
     * | Password Reminder Language Lines
     * |--------------------------------------------------------------------------
     * |
     * | The following language lines are the default lines which match reasons
     * | that are given by the password broker for a password update attempt
     * | has failed, such as for an invalid token or invalid new password.
     * |
     */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $sharedManager = $eventManager->getSharedManager();
        $sm = $e->getApplication()->getServiceManager();

        $eventManager->attachAggregate($sm->get('Application\Listener\ViewListener'));

        // $sm->get('translator');
        $this->initTranslator($e);

        /*
         * when Dispatched, attach listener to Event manager
         * shared event manager will not trigger event
         *
         */

        // AbtractController is EventManagerAware.
        $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController', MvcEvent::EVENT_DISPATCH, function ($e) use ($sm) {
            /**@var \Zend\Mvc\Controller\AbstractActionController $controller ; */
            $controller = $e->getTarget();

            // new in zend 3, replace EventManager->attachAgrregate ($listener);
            // $sm->get ( 'Application\Listener\PictureUploadListener' )->attach ( $controller->getEventManager () );

            // Version 2
            $pictureUploadListener = $sm->get('Application\Listener\PictureUploadListener');
            $controller->getEventManager()
                ->attachAggregate($pictureUploadListener);

            // Logging
            $LoggingListener = $sm->get('Application\Listener\LoggingListener');
            $controller->getEventManager()
                ->attachAggregate($LoggingListener);

            // FINANCE Module Listener
            $financeListener = $sm->get('Application\Listener\FinanceListener');
            $controller->getEventManager()
                ->attachAggregate($financeListener);

            // PROCURE Module Listener
            $procureListener = $sm->get('Application\Listener\ProcureListener');
            $controller->getEventManager()
                ->attachAggregate($procureListener);

            // change module layout
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $controller->layout($moduleNamespace . '/layout-fluid');
        }, 101);
    }

    /**
     *
     * @param MvcEvent $event
     */
    private function initUserPreferences(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();
        $viewModel = $event->getApplication()
            ->getMvcEvent()
            ->getViewModel();

        // Zend\Session\Container
        $session = new Container('_USER_PREFERENCE_');
        $translator = $serviceManager->get('translator');

        if ($session->showLeftMenu == null) {
            $session->showLeftMenu = 'show';
            $currentLocale = 'en_US';
        } else {
            $currentLocale = $session->locale;
        }

        $viewModel->currentLocale = $currentLocale;
        $translator->setLocale($currentLocale)->setFallbackLocale('en_US');

    /**
     *
     * @todo use in production
     */
        // $translator->setCache($serviceManager->get('FileSystemCache'));
    }

    /**
     *
     * @param MvcEvent $event
     */
    private function initTranslator(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();
        $viewModel = $event->getApplication()
            ->getMvcEvent()
            ->getViewModel();

        $config = $serviceManager->get('config');

        if (isset($config['locale']['available'])) {
            $viewModel->availableLocale = $config['locale']['available'];
        } else {
            $viewModel->availableLocale = null;
        }

        // Zend\Session\Container
        $session = new Container('locale');
        $translator = $serviceManager->get('translator');

        if ($session->locale == null) {
            $session->locale = 'en_US';
            $currentLocale = 'en_US';
        } else {
            $currentLocale = $session->locale;
        }

        $viewModel->currentLocale = $currentLocale;
        $translator->setLocale($currentLocale)->setFallbackLocale('en_US');

    /**
     *
     * @todo use in production
     */
        // $translator->setCache($serviceManager->get('FileSystemCache'));
    }

    /**
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     *
     * @return string[][][]
     */
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
     * always put closure here.
     *
     * @return NULL[][]
     */
    public function getServiceConfig()
    {
        return array(

            'aliases' => array(
                'Zend\Authentication\AuthenticationService' => 'AuthService'
            ),

            'factories' => array(

                // Authentication Service
                'AuthService' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'mla_users', 'email', 'password', 'MD5(?)');
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    return $authService;
                },

                /**
                 *
                 * @deprecated
                 */
                'Application\Model\AclRoleTable' => function ($container) {
                    $tableGateway = $container->get('AclRoleTableGateway');
                    return new AclRoleTable($tableGateway);
                },

                /**
                 *
                 * @deprecated
                 */
                'AclRoleTableGateway' => function ($container) {
                    $dbAdapter = $container->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AclRole());
                    return new TableGateway('nmt_application_acl_role', $dbAdapter, null, $resultSetPrototype);
                }
            )
        );
    }
}
