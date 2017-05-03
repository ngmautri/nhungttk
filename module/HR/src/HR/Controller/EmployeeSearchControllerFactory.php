<?php

namespace HR\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use HR\Controller\EmployeeSearchController;
/**
 * 
 * @author nmt
 *
 */
class EmployeeSearchControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$container= $serviceLocator->getServiceLocator();
		$controller = new EmployeeSearchController();
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
		$sv =  $container->get('HR\Service\EmployeeSearchService');
		$controller->setEmployeeSearchService($sv);
		
		return $controller;
	}
}