<?php

namespace BP\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use BP\Service\VendorSearchService;

/*
 * @author nmt
 *
 */
class VendorSearchServiceFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container = $serviceLocator;
		
		$service = new VendorSearchService();
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$service->setDoctrineEM($sv);
		return $service;
	}
}