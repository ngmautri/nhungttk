<?php

namespace Procure\Controller;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * 
 * @author nmt
 *
 */
class PoAttachmentControllerFactory implements FactoryInterface{
	
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container= $serviceLocator->getServiceLocator();
		
		
		$controller= new PoAttachmentController();
			
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
		$sv =  $container->get('ProcureLogger');
		$controller->setLogger($sv);
		
		
		return $controller;
	}	
	
}