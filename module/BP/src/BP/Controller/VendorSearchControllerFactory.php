<?php

namespace BP\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use BP\Controller\VendorSearchController;

/*
 * @author nmt
 *
 */
class VendorSearchControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new VendorSearchController();
			
		$sv =  $sm->get ('doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM($sv );
		
		//Vendor Search Service
		$sv =  $sm->get ('BP\Service\VendorSearchService' );
		$controller->setVendorSearchService($sv );
		
		
		
		return $controller;
	}
}