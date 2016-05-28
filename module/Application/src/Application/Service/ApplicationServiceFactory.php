<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Service\ApplicationService;

/*
 * @author nmt
 *
 */
class ApplicationServiceFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
			
		$s = new ApplicationService();
		
		$sv =  $serviceLocator->get ('ModuleManager' );
		$s->setModuleManager($sv);
		
		$sv =  $serviceLocator->get ('ControllerLoader' );
		$s->setControllerManager($sv);
		return $s;
	}
}