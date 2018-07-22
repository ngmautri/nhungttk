<?php

namespace Inventory\Service;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemSerialSearchServiceFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container = $serviceLocator;
		
		$service = new ItemSerialSearchService();
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$service->setDoctrineEM($sv);
		
		$grListener = $container->get('Application\Listener\LoggingListener');		
		$eventManager =  $container->get('EventManager');
		
		$eventManager->attachAggregate($grListener);
		$service->setEventManager($eventManager);
		
		
		return $service;
	}
}