<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
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

class Module
{

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $sharedManager = $eventManager->getSharedManager();
        $sm = $e->getApplication()->getServiceManager();
        
        //$sm->get('translator');
        $this->initTranslator($e);
        
        
        /*
         * when Dispatched, attach listener to Event manager
         * shared event manager will not trigger event
         *
         */
        $sharedManager->attach('Zend\Mvc\Controller\AbstractActionController', MvcEvent::EVENT_DISPATCH, function ($e) use ($sm) {
            $controller = $e->getTarget();
            
            // new in zend 3, replace EventManager->attachAgrregate ($listener);
            // $sm->get ( 'Application\Listener\PictureUploadListener' )->attach ( $controller->getEventManager () );
            
            // Version 2
            $pictureUploadListener = $sm->get('Application\Listener\PictureUploadListener');
            $controller->getEventManager()
                ->attachAggregate($pictureUploadListener);
            
            $LoggingListener = $sm->get('Application\Listener\LoggingListener');
            $controller->getEventManager()
                ->attachAggregate($LoggingListener);
            
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
        
        // Zend\Session\Container
        $session = New Container('locale');
        $translator = $serviceManager->get('translator');
        $translator->setLocale($session->locale)
        ->setFallbackLocale('en_US');
        // only in production
        //$translator->setCache($serviceManager->get('FileSystemCache'));
        
    }
    /**
     *
     * @return unknown
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
     * @return NULL[][]
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Zend\Db\Adapter\Adapter' => function ($sm) {
                    return new Adapter(array(
                        'driver' => 'Pdo_Mysql',
                        'hostname' => 'localhost',
                        'database' => 'mla',
                        'username' => 'root',
                        'password' => 'kflg79'
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
                
                /**@deprecated */
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
