<?php

namespace Procure\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Procure\Controller\PrSearchController;
/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GrSearchControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new GrSearchController();
			
		$sv =  $sm->get ('doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM($sv );
		
		//Vendor Search Service
		$sv =  $sm->get ('Procure\Service\PrSearchService' );
		$controller->setPrSearchService($sv );
		
		
		
		return $controller;
	}
}