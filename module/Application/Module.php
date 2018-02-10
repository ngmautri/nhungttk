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
use Application\Model\AclRoleTable;
use Application\Model\AclRole;

class Module {
	
	public function onBootstrap(MvcEvent $e) {
		$eventManager = $e->getApplication ()->getEventManager ();
		$moduleRouteListener = new ModuleRouteListener ();
		$moduleRouteListener->attach ( $eventManager );
		
		$sharedManager = $eventManager->getSharedManager ();
		$sm = $e->getApplication ()->getServiceManager ();
		
		/*
		 * when Dispatched, attach listener to Event manager
		 * shared event manager will not trigger event
		 *
		 */
		$sharedManager->attach ( 'Zend\Mvc\Controller\AbstractActionController', MvcEvent::EVENT_DISPATCH, function ($e) use ($sm) {
			$controller = $e->getTarget ();
			
			// new in zend 3, replace EventManager->attachAgrregate ($listener);
			//$sm->get ( 'Application\Listener\PictureUploadListener' )->attach ( $controller->getEventManager () );
			
			//Version 2
			$pictureUploadListener = $sm->get ( 'Application\Listener\PictureUploadListener' );
			$controller->getEventManager()->attachAggregate ( $pictureUploadListener );
		
			$LoggingListener = $sm->get ( 'Application\Listener\LoggingListener' );
			$controller->getEventManager()->attachAggregate ( $LoggingListener );
			
			
			$controllerClass = get_class($controller);
			$moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
			$controller->layout($moduleNamespace . '/layout-fluid');
		}, 101 );
			
	}
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
	public function getAutoloaderConfig() {
		return array (
				'Zend\Loader\StandardAutoloader' => array (
						'namespaces' => array (
								__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__ 
						) 
				) 
		);
	}
	public function getServiceConfig() {
		return array (
				'factories' => array (
						
						// Role Table
						'Application\Model\AclRoleTable' => function ($container) {
							$tableGateway = $container->get ( 'AclRoleTableGateway' );
							return new AclRoleTable ( $tableGateway );
						},
						
						'AclRoleTableGateway' => function ($container) {
							$dbAdapter = $container->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new AclRole () );
							return new TableGateway ( 'nmt_application_acl_role', $dbAdapter, null, $resultSetPrototype );
						} 
				
				),
				
				'invokables' => array () 
		
		);
	}
}
