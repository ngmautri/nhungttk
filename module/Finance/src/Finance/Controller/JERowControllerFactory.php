<?php

namespace Finance\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * 02/07
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class JERowControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new JERowController();
			
		$sv =  $sm->get ('doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM($sv );
		
		$sv =  $sm->get('Finance\Service\JEService');
		$controller->setJeService($sv);
				
		
		return $controller;
	}
}