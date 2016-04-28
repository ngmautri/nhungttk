<?php

namespace Procurement\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Procurement\Controller\VendorController;

/*
 * @author nmt
 *
 */
class VendorControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new VendorController();		
		// Purchase Request table
		$tbl =  $sm->get ('Procurement\Model\PurchaseRequestTable' );
		$controller->setPurchaseRequestTable($tbl);
		
		// Purchase Request Item table
		$tbl =  $sm->get ('Procurement\Model\PurchaseRequestItemTable' );
		$controller->setPurchaseRequestItemTable($tbl);

			
		// Delivery Item table
		$tbl =  $sm->get ('Procurement\Model\DeliveryItemTable' );
		$controller->setDeliveryItemTable($tbl);
		
		// Delivery table
		$tbl =  $sm->get ('Procurement\Model\DeliveryTable' );
		$controller->setDeliveryTable($tbl);
		
		// Vendor table
		$tbl =  $sm->get ('Procurement\Model\VendorTable' );
		$controller->setVendorTable($tbl);
		
		
		// User table
		$tbl =  $sm->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
			
		return $controller;
	}
}