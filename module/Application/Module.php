<?php

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Application\Model\AclRoleTable;
use Application\Model\AclRole;
use Zend\Db\Adapter\Adapter;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter as DbTableAuthAdapter;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class Module
{

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
                'Zend\Db\Adapter\Adapter' => function ($sm) {
                    return new Adapter(array(
                        'driver' => 'Pdo_Mysql',
                        'hostname' => 'localhost',
                        'database' => 'mla',
                        'username' => 'root',
                        'password' => 'NMTerfolgkflg#7986'
                    ));
                },
                
                // Authentication Service
                'AuthService' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'mla_users', 'email', 'password', 'MD5(?)');
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    return $authService;
                },
                
                // Email Service
                'SmtpTransportService' => function ($sm) {
                    
                    $transport = new SmtpTransport();
                    $options = new SmtpOptions(array(
                        'name' => 'Web.de',
                        'host' => 'smtp.web.de',
                        'port' => '587',
                        'connection_class' => 'login',
                        'connection_config' => array(
                            'username' => 'mib-team@web.de',
                            'password' => 'mib2009',
                            'ssl' => 'tls'
                        )
                    ));
                    
                    $transport->setOptions($options);
                    return $transport;
                },
                
                // Email Service
                'SmtpTransportService1' => function ($sm) {
                    
                    $transport = new SmtpTransport();
                    $options = new SmtpOptions(array(
                        'name' => 'Outlook.com',
                        'host' => 'smtp-mail.outlook.com',
                        'port' => '587',
                        'connection_class' => 'login',
                        'connection_config' => array(
                            'username' => 'mla-web@outlook.com',
                            'password' => 'MLA#2016',
                            'ssl' => 'tls'
                        )
                    ));
                    
                    $transport->setOptions($options);
                    return $transport;
                },
                
                /**
                 *
                 * @deprecated
                 */
                'Application\Model\AclRoleTable' => function ($container) {
                    $tableGateway = $container->get('AclRoleTableGateway');
                    return new AclRoleTable($tableGateway);
                },
                
                /**@deprecated */
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
