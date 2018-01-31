<?php

namespace Procure\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Controller\ConsoleController;

/*
 * @author nmt
 *
 */
class PrConsoleControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new PrConsoleController ();
		
		//Auth Service
		$sv =  $sm->get ('SmtpTransportService' );
		$controller->setSmtpTransportService($sv);
		
		
		return $controller;
	}
}