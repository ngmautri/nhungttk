<?php

namespace HR\Services;

use Zend\EventManager\EventManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use HR\Services\VendorSearchService;



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
		
		$eventManager = $serviceLocator->get ( 'EventManager' );
				
		$searchService = new VendorSearchService();
		$searchService->setEventManager ( $eventManager );
		
		$tbl =  $serviceLocator->get ('HR\Model\VendorTable' );
		
		$searchService->setVendorTable($tbl);
		
		return $searchService;
	}
}