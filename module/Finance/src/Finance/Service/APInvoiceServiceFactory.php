<?php

namespace Procure\Service;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Finance\Service\APInvoiceService;

/*
 * @author nmt
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
		
		$grListener = $container->get('Order\Listener');
	
		$eventManager =  $container->get('EventManager');
		$eventManager->attachAggregate($grListener);
		
		$service->setEventManager($eventManager);
		
		return $service;
	}
}