<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Calendar;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;

class Module {
	
	/*
	 * The init() method is called for every module implementing this feature, on every page request,
	 * and should only be used for performing lightweight tasks such as registering event listeners.
	 */
	
	/*
	 * public function init(ModuleManager $moduleManager)
	 * {
	 * // Remember to keep the init() method as lightweight as possible
	 * $events = $moduleManager->getEventManager();
	 * $events->attach('loadModules.post', array($this, 'modulesLoaded'));
	 * }
	 */
	
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
	 * @param ModuleManager $mm
	 */
	/**
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
	
	
	/**
	 * 
	 * @return unknown
	 */
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
