<?php

namespace User\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use User\Controller\InboxController;

/*
 * @author nmt
 *
 */
class InboxControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new InboxController();
		
		$sv =  $sm->get ('doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM($sv );
		
		
		return $controller;
	}
}