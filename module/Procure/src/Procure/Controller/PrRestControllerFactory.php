<?php

namespace Procure\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Procure\Controller\PrController;

/**
 * 
 * @author nmt
 *
 */
class PrRestControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new PrRestController();
			
		$sv =  $sm->get ('doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM($sv );
		
		return $controller;
	}
}