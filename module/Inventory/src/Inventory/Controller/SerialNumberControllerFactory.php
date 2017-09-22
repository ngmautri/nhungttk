<?php

namespace Inventory\Controller;

use Inventory\Controller\SerialNumberController;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * 
 * @author nmt
 *
 */
class SerialNumberControllerFactory implements FactoryInterface{
	
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container= $serviceLocator->getServiceLocator();
		
		$controller= new SerialNumberController();
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
		$sv =  $container->get('Inventory\Service\ItemSearchService');
		$controller->setItemSearchService($sv);
		
		return $controller;
	}	
	
}