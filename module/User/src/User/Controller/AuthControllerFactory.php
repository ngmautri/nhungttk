<?php

namespace User\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use User\Controller\AuthController;

/*
 * @author nmt
 *
 */
class AuthControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new AuthController();
		
		//User Table
		$tbl =  $sm->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
			
		//Auth Service
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
		
		$sv =  $sm->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
			
		return $controller;
	}
}