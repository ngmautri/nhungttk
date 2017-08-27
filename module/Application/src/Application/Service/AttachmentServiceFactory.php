<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/*
 * @author nmt
 *
 */
class AttachmentServiceFactory implements FactoryInterface {
	
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
	
		$container = $serviceLocator;
		
		$service = new AttachmentService();
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$service->setDoctrineEM($sv);
		
		return $service;
	}
}