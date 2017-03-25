<?php

namespace Inventory\Services;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Services\SparepartService;
/*
 * @author nmt
 *
 */
class SparepartServiceFactory implements FactoryInterface {
	
	
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sv = new SparepartService();
			
		return $sv;
	}
}