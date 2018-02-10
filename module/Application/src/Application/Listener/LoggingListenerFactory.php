<?php

namespace Application\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/*
 * @author nmt
 *
 */
class LoggingListenerFactory implements FactoryInterface {
	
		/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$container = $serviceLocator;		
		$listener = new LoggingListener();
		return $listener;
	}
}