<?php

namespace Inventory\Services;

use Zend\EventManager\EventManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Services\AssetService;

/*
 * @author nmt
 *
 */
class ArticleServiceFactory implements FactoryInterface {
	
	
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
			$sv = new AssetService();
			return $sv;
	}
}