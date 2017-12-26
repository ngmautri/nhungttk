<?php

namespace Application\Controller;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/*
 * @author nmt
 *
 */
class SearchIndexControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$container = $serviceLocator->getServiceLocator ();
			
		$controller = new SearchIndexController();
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
		$sv =  $container->get ('Inventory\Service\ItemSearchService' );
		$controller->setItemSearchService($sv );
		
		$sv =  $container->get ('Procure\Service\PrSearchService' );
		$controller->setPrSearchService($sv );
		
		$sv =  $container->get ('PM\Service\ProjectSearchService' );
		$controller->setProjectSearchService($sv );
							
		return $controller;
	}
}