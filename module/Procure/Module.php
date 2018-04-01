<?php

/**
 * Configuration Module: Procurement
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
namespace Procure;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;

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
	
	/**
	 * 
	 * @param ModuleManager $mm
	 */
	public function init(ModuleManager $mm)
	{
		/* $mm->getEventManager()->getSharedManager()->attach(__NAMESPACE__,
				'dispatch', function($e) {
				
				$request=$e->getTarget()->getRequest ();
				if ($request->isXmlHttpRequest ()) {
					$e->getTarget()->layout('layout/user/ajax');
				}else{
					$e->getTarget()->layout('procure/layout-fluid');
				}
				}); */
		
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
