<?php

namespace BP\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use BP\Controller\DebtorController;

/*
 * @author nmt
 *
 */
class DebtorControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new DebtorController();
			
		$sv =  $sm->get ('doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM($sv );
		
		
		return $controller;
	}
}