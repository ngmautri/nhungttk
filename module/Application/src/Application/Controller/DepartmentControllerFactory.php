<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Controller\DepartmentControllerController;

/*
 * @author nmt
 *
 */
class DepartmentControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new DepartmentController();
		
		//User Table
		$tbl =  $sm->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		//Department Table
		$tbl =  $sm->get ('Application\Model\DepartmentTable' );
		$controller->setDepartmentTable( $tbl );

		//Department Member Table
		$tbl =  $sm->get ('Application\Model\DepartmentMemberTable' );
		$controller->setDepartmentMemberTable($tbl );
		
		
		//Auth Service
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
		return $controller;
	}
}