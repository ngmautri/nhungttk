<?php

namespace Workflow\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Workflow\Service\WorkflowService;

/*
 * @author nmt
 *
 */
class WorkflowServiceFactory implements FactoryInterface {
	
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
	
		$container = $serviceLocator;
		
		$service = new WorkflowService();
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$service->setDoctrineEM($sv);
		
		return $service;
	}
}