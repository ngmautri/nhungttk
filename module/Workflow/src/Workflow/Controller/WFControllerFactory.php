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
		
		//Workflow Table
		$tbl =  $sm->get ('Workflow\Model\WorkflowTable' );
		$controller->setWorkflowTable($tbl);
		
		//Auth Service
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
		return $controller;
	}
}