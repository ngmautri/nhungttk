<?php

namespace Procure\Listener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GrListenerFactory implements FactoryInterface {
	
		/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$container = $serviceLocator;		
		$listener = new GrListener();
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$listener->setDoctrineEM($sv);
		return $listener;
	}
}