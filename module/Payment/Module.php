<?php

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
namespace Payment;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module {
	
	/*
	 * The onBootstrap() method is called for every module implementing this feature, on every page request,
	 * and should only be used for performing lightweight tasks such as registering event listeners.
	 */
	public function onBootstrap(MvcEvent $e) {
		$eventManager = $e->getApplication ()->getEventManager ();
		$moduleRouteListener = new ModuleRouteListener ();
		$moduleRouteListener->attach ( $eventManager );
	
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
	
	// Add this method:
	public function getServiceConfig() {
		return array (
				'factories' => array (
						)
		);
	}
}
