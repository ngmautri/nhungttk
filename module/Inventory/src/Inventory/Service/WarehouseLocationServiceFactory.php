<?php

namespace Inventory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * 
 * @author Nguyen Mau Tri
 *
 */
class WarehouseLocationServiceFactory implements FactoryInterface {
	
	
		/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$container = $serviceLocator;
			
	
		$service = new WarehouseLocationService();
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$service->setDoctrineEM($sv);
		
		return $service;
	}
}