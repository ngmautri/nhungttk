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
		$controller->setPurchaseRequetTable($tbl);
		
		// Purchase Request Item table
		$tbl =  $sm->get ('Procurement\Model\PurchaseRequestItemTable' );
		$controller->setPurchaseRequetItemTable($tbl);

		// Purchase Request Item Pic table
		$tbl =  $sm->get ('Procurement\Model\PurchaseRequestItemPicTable' );
		$controller->setPurchaseRequetItemPicTable($tbl);
		
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
			
		return $controller;
	}
}