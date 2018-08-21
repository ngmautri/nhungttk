<?php

namespace Inventory\Controller;


use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OpeningBalanceControllerFactory implements FactoryInterface{
	
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container= $serviceLocator->getServiceLocator();
		
		$controller= new OpeningBalanceController();
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
		$sv =  $container->get('Inventory\Service\ItemSearchService');
		$controller->setItemSearchService($sv);
		
		$sv =  $container->get('Inventory\Service\OpeningBalanceService');
		$controller->setObService($sv);
		
		return $controller;
	}	
	
}