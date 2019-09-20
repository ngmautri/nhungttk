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
class PoRowControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new PoRowController();
			
		$sv =  $sm->get ('doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM($sv );
		
		/* //Vendor Search Service
		$sv =  $sm->get ('Procure\Service\PrSearchService' );
		$controller->setPrSearchService($sv ); */
		
		$sv =  $sm->get ('Procure\Service\PoService' );
		$controller->setPoService($sv);
		
		$sv =  $sm->get ('Procure\Service\PoSearchService' );
		$controller->setPoSearchService($sv );

		$sv =  $sm->get ('Procure\Application\Reporting\PO\PoReporter' );
		$controller->setPoReporter($sv);		
		
		return $controller;
	}
}