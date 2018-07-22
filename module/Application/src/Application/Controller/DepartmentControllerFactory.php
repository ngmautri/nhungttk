<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
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
		
			
		$tbl =  $container->get ('Application\Model\AclRoleTable' );
		$controller->setAclRoleTable($tbl);
		
    	//Auth Service
		$sv =  $container->get ('Application\Service\DepartmentService' );
		$controller->setDepartmentService($sv );
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
		
		return $controller;
	}
}