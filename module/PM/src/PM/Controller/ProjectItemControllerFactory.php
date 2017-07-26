<?php

namespace PM\Controller;

use PM\Controller\ProjectItemController;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * 
 * @author nmt
 *
 */
class ProjectItemControllerFactory implements FactoryInterface{
	
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container= $serviceLocator->getServiceLocator();
		
		
		$controller= new ProjectItemController();
			
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
		return $controller;
	}	
	
}