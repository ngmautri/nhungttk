<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
		
		$container = $serviceLocator;
		
		$s = new ApplicationService();
		
		$sv =  $container->get ('ModuleManager' );
		$s->setModuleManager($sv);
		
		$sv =  $container->get ('ControllerManager' );
		$s->setControllerManager($sv);
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$s->setDoctrineEM($sv);
		
		return $s;
	}
}