<?php

namespace Application\Controller;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompanyControllerFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$container = $serviceLocator->getServiceLocator ();
		
		$controller = new CompanyController ();
		
			
		$sv = $container->get ( 'doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM ( $sv );
		
		return $controller;
	}
}