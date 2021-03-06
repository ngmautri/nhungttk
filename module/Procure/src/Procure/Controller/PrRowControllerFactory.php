<?php

namespace Procure\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Procure\Controller\PrRowController;

/**
 * 
 * @author nmt
 *
 */
class PrRowControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new PrRowController();
			
		$sv =  $sm->get ('doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM($sv );
		
		//Vendor Search Service
		$sv =  $sm->get ('Procure\Service\PrSearchService' );
		$controller->setPrSearchService($sv );
	
		//Vendor Search Service
		$sv =  $sm->get ('Procure\Service\PrService' );
		$controller->setPrService($sv );
		
		$sv =  $sm->get('FileSystemCache');
		$controller->setCacheService($sv);
	
		//Vendor Search Service
		$sv =  $sm->get ('Procure\Application\Reporting\PR\PrRowStatusReporter' );
		$controller->setPrRowStatusReporter($sv);
		
		
		return $controller;
	}
}