<?php

namespace BP\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use BP\Controller\VendorContractController;

/*
 * @author nmt
 *
 */
class VendorContractControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new VendorContractController();
			
		$sv =  $sm->get ('doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM($sv );
		
		
		return $controller;
	}
}