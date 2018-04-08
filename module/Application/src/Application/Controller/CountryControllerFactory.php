<?php

namespace Application\Controller;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */

class CountryControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$container = $serviceLocator->getServiceLocator ();
		$controller = new CountryController ();
		
		$sv = $container->get ( 'doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM ( $sv );
		
		return $controller;
	}
}