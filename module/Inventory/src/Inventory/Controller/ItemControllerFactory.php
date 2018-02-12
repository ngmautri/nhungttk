<?php

namespace Inventory\Controller;

use Inventory\Controller\ItemController;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * 
 * @author nmt
 *
 */
class ItemControllerFactory implements FactoryInterface{
	
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container= $serviceLocator->getServiceLocator();
		
		
		$controller= new ItemController();
		
		
		// User Table
		$tbl = $container->get ( 'User\Model\UserTable' );
		$controller->setUserTable ( $tbl );
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
		$sv =  $container->get('Inventory\Service\ItemSearchService');
		$controller->setItemSearchService($sv);
		
		$sv =  $container->get('FileSystemCache');
		$controller->setCacheService($sv);
		
		return $controller;
	}	
	
}