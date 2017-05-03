<?php

namespace HR\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use HR\Controller\EmployeeController;

/**
 * 
 * @author nmt
 *
 */
class EmployeeControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$container= $serviceLocator->getServiceLocator();
		$controller = new EmployeeController();
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		return $controller;
	}
}