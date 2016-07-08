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
		
		// PRItemWorkFlow table
		$tbl =  $sm->get ('Procurement\Model\PRItemWorkFlowTable' );
		$controller->setPRItemWorkflowTable($tbl);
		
		// Delivery WF table
		$tbl =  $sm->get ('Procurement\Model\DeliveryWorkFlowTable' );
		$controller->setDeliveryWorkFlowTable($tbl);
		
		// Delivery Item WF table
		$tbl =  $sm->get ('Procurement\Model\DeliveryItemWorkFlowTable' );
		$controller->setDeliveryItemWorkFlowTable($tbl);
		
		// Delivery Cart table
		$tbl =  $sm->get ('Procurement\Model\DeliveryCartTable' );
		$controller->setDeliveryCartTable($tbl);		
		
		// Article Movement Table
		$tbl =  $sm->get ('Inventory\Model\ArticleMovementTable' );
		$controller->setArticleMovementTable($tbl);
		
		// Article Last DN Table
		$tbl =  $sm->get ('Inventory\Model\ArticleLastDNTable' );
		$controller->setArticleLastDNTable($tbl);
		
		// Sparepart Sparepare Table
		$tbl =  $sm->get ('Inventory\Model\SparepartMovementsTable' );
		$controller->setSparepartMovementTable($tbl);
		
		// Sparepart Last DN Table
		$tbl =  $sm->get ('Inventory\Model\SparepartLastDNTable' );
		$controller->setSparepartLastDNTable($tbl);
		
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