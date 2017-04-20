<?php

namespace Application\Listener;

use Application\Listener\PictureUploadListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


/*
 * @author nmt
 *
 */
class PictureUploadListenerFactory implements FactoryInterface {
	
		/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$container = $serviceLocator;		
		$listener = new PictureUploadListener();
		return $listener;
	}
}