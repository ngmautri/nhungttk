<?php

namespace Finance\Service;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @deprecated
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class APInvoiceServiceFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container = $serviceLocator;
		
		$service = new APInvoiceService();
		
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