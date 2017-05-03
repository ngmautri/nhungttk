<?php

namespace HR\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Controller\SparepartsController;

/**
 * @deprecated
 * @author nmt
 *
 */
class ImageControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new ImageController;
		
		//User Table
		$tbl =  $sm->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		
		// Employee table
		$tbl =  $sm->get ('HR\Model\EmployeeTable' );
		$controller->setEmployeeTable($tbl);
		
		// Employee Picture Table
		$tbl =  $sm->get ('HR\Model\EmployeePictureTable' );
		$controller->setEmployeePictureTable($tbl);
		
		
		//Auth Service
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
		//Auth Service
		$sv =  $sm->get ('HR\Services\EmployeeService' );
		$controller->setEmployeeService($sv );
		
		
		return $controller;
	}
}