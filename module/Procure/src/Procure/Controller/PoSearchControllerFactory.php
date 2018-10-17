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
class PoSearchControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new PoSearchController();
			
		$sv =  $sm->get ('doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM($sv );
		
			$sv =  $sm->get ('Procure\Service\PoSearchService' );
		$controller->setPoSearchService($sv );
		
		return $controller;
	}
}