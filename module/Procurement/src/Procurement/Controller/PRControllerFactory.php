<?php

namespace Procurement\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Procurement\Controller\PRController;

/*
 * @author nmt
 *
 */
class PRControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new PRController();
		
		// Purchase Request table
		$tbl =  $sm->get ('Procurement\Model\PurchaseRequestTable' );
		$controller->setPurchaseRequestTable($tbl);
		
		// Purchase Request Item table
		$tbl =  $sm->get ('Procurement\Model\PurchaseRequestItemTable' );
		$controller->setPurchaseRequestItemTable($tbl);

		// Purchase Request Item Pic table
		$tbl =  $sm->get ('Procurement\Model\PurchaseRequestItemPicTable' );
		$controller->setPurchaseRequestItemPicTable($tbl);
		
		// User table
		$tbl =  $sm->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
			
		return $controller;
	}
}