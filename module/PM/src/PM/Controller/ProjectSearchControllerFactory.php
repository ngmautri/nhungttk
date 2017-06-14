<?php

namespace PM\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use PM\Controller\ProjectSearchController;

/*
 * @author nmt
 *
 */
class ProjectSearchControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new ProjectSearchController();
			
		$sv =  $sm->get ('doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM($sv );
		
		//Vendor Search Service
		$sv =  $sm->get ('PM\Service\ProjectSearchService' );
		$controller->setProjectSearchService($sv );
		
		
		
		return $controller;
	}
}