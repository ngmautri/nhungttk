<?php

namespace Payment\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * 02/07
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ActivityLogControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$container= $serviceLocator->getServiceLocator();
		$controller = new ActivityLogController();
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		return $controller;
	}
}