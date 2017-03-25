<?php

namespace Inventory\Services;

use Zend\EventManager\EventManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Services\ArticleService;

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
		
		// get RegisterListener
			$sv = new ArticleService();
			return $sv;
	}
}