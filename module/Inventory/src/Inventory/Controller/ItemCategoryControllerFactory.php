<?php

namespace Inventory\Controller;

use Inventory\Controller\ItemCategoryController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/*
 * @author nmt
 *
 */
class ItemCategoryControllerFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$container = $serviceLocator->getServiceLocator ();
			
		$controller = new ItemCategoryController();
		
		
		//Auth Service
		$sv =  $container->get ('Application\Service\ItemCategoryService' );
		$controller->setItemCategoryService($sv );
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
		return $controller;
	}
}