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
	
		$service = new WorkflowService();
		
		$tbl =  $serviceLocator->get ('Workflow\Model\NmtWfNodeTable' );
		$service->setWorkFlowNoteTable($tbl);
		return $service;
	}
}