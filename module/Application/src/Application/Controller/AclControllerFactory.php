<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Controller\ACLController;

/*
 * @author nmt
 *
 */
class AclControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new AclController();
		
		//User Table
		$tbl =  $sm->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		//User Table
		$tbl =  $sm->get ('User\Model\AclResourceTable' );
		$controller->setAclResourceTable($tbl);
		
		//Auth Service
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
		//Auth Service
		$sv =  $sm->get ('Application\Service\ApplicationService' );
		$controller->setAppService($sv );
		
		
		return $controller;
	}
}