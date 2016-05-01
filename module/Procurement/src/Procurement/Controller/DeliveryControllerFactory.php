<?php

namespace Procurement\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Procurement\Controller\DeliveryController;

/*
 * @author nmt
 *
 */
class DeliveryControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new DeliveryController();		
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
		
		// Delivery table
		$tbl =  $sm->get ('Procurement\Model\PRItemWorkFlowTable' );
		$controller->setPRItemWorkflowTable($tbl);
		
		// Delivery WF table
		$tbl =  $sm->get ('Procurement\Model\DeliveryWorkFlowTable' );
		$controller->setDeliveryWorkFlowTable($tbl);
		
		
		// Department table
		$tbl =  $sm->get ('Application\Model\DepartmentTable' );
		$controller->setDepartmentTable($tbl);
		
		// User table
		$tbl =  $sm->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
			
		return $controller;
	}
}