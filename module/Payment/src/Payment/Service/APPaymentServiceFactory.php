<?php

namespace Payment\Service;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class APPaymentServiceFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container = $serviceLocator;
		
		$service = new APPaymentService();
		
		$sv =  $container->get('ControllerPluginManager');
		$service->setControllerPlugin($sv->get('NmtPlugin'));
		
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$service->setDoctrineEM($sv);
		
		$sv =  $container->get('Finance\Service\JEService');
		$service->setJeService($sv);
		
		$grListener = $container->get('Application\Listener\LoggingListener');
	
		$eventManager =  $container->get('EventManager');
		$eventManager->attachAggregate($grListener);
		
		$service->setEventManager($eventManager);
		
		return $service;
	}
}