<?php

namespace Application\Controller;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Application\Controller\CompanyController;

/*
 * @author nmt
 *
 */
class CompanyControllerFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$container = $serviceLocator->getServiceLocator ();
		
		$controller = new CompanyController ();
		
		// User Table
		$tbl = $container->get ( 'User\Model\UserTable' );
		$controller->setUserTable ( $tbl );
		
		// Auth Service
		$sv = $container->get ( 'AuthService' );
		$controller->setAuthService ( $sv );
		
		$sv = $container->get ( 'doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM ( $sv );
		
		/*
		$sv = $container->get ( 'Application\Listener\PictureUploadListener' );
		$controller->setPicUpdateListener ( $sv );
		*/
		return $controller;
	}
}