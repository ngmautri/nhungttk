<?php

namespace Application\Controller;

use Application\Controller\SearchController;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/*
 * @author nmt
 *
 */
class SearchControllerFactory implements FactoryInterface {
	
	/**
	*
	* {@inheritDoc}
	* @see \Zend\ServiceManager\FactoryInterface::createService()
	*/
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container = $serviceLocator->getServiceLocator();
			
		$controller = new SearchController();
		
		//User Table
		$tbl =  $container->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		//User Table
		/* $tbl =  $sm->get ('User\Model\AclResourceTable' );
		$controller->setAclResourceTable($tbl);
		 */
		//Auth Service
		$sv =  $container->get ('AuthService' );
		$controller->setAuthService($sv );
		
		//Auth Service
		$sv =  $container->get ('Application\Service\AppSearchService' );
		$controller->setAppSearchService($sv );
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
		
		return $controller;
	}
}