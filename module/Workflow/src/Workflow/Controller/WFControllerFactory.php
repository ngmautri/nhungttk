<?php

namespace Workflow\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Workflow\Controller\WFController;

/*
 * @author nmt
 *
 */
class WFControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new WFController();
		$sv =  $sm->get ('doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM($sv );
				
	    $sv =  $sm->get ('Workflow\Service\WorkflowService' );
		$controller->setWfService($sv);
				
		return $controller;
	}
}