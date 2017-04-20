<?php

namespace Application\Controller;

use Application\Controller\DepartmentController;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/*
 * @author nmt
 *
 */
class DepartmentControllerFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container = $serviceLocator->getServiceLocator ();
			
			
		$controller = new DepartmentController();
		
		//User Table
		$tbl =  $container->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		$tbl =  $container->get ('Application\Model\AclRoleTable' );
		$controller->setAclRoleTable($tbl);
		
		//Auth Service
		$sv =  $container->get ('AuthService' );
		$controller->setAuthService($sv );
		
		//Auth Service
		$sv =  $container->get ('Application\Service\DepartmentService' );
		$controller->setDepartmentService($sv );
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
		
		return $controller;
	}
}