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
class EmployeeSearchServiceFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$eventManager = $serviceLocator->get ( 'EventManager' );
				
		$searchService = new EmployeeSearchService();
		$searchService->setEventManager ( $eventManager );
		
		$tbl =  $serviceLocator->get ('HR\Model\EmployeeTable' );
		
		$searchService->setEmployeeTable($tbl);
		
		return $searchService;
	}
}