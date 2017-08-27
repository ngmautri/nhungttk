<?php

namespace Finance\Controller;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * 
 * @author nmt
 *
 */
class VInvoiceAttachmentControllerFactory implements FactoryInterface{
	
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container= $serviceLocator->getServiceLocator();
		
		
		$controller= new VInvoiceAttachmentController();
			
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
		return $controller;
	}	
	
}