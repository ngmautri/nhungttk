<?php

namespace Application\Controller;

use Application\Controller\PmtMethodController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/*
 * @author nmt
 *
 */
class PmtMethodControllerFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$container = $serviceLocator->getServiceLocator ();
			
		$controller = new PmtMethodController();
		
		//User Table
		$tbl =  $container->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		//Auth Service
		$sv =  $container->get ('AuthService' );
		$controller->setAuthService($sv );
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
		
		return $controller;
	}
}