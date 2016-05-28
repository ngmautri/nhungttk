<?php

namespace User\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use User\Controller\RoleController;

/*
 * @author nmt
 *
 */
class RoleControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new RoleController();
		
		//User Table
		$tbl =  $sm->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		//User Role Table
		$tbl =  $sm->get ('User\Model\AclUserRoleTable' );
		$controller->setAclUserRoleTable($tbl);
		
		//Res Table
		$tbl =  $sm->get ('User\Model\AclResourceTable' );
		$controller->setAclResourceTable($tbl);
		
		//Role-Res Table
		$tbl =  $sm->get ('User\Model\AclRoleResourceTable' );
		$controller->setAclRoleResourceTable($tbl);
		
		//Role Table
		$tbl =  $sm->get ('User\Model\AclRoleTable' );
		$controller->setAclRoleTable($tbl);
		
		
		//Auth Service
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
		//Auth Service
		$sv =  $sm->get ('User\Service\Acl' );
		$controller->setAclService($sv );
		
		return $controller;
	}
}