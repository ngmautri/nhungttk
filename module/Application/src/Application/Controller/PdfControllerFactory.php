<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Controller\PdfController;


/*
 * @author nmt
 *
 */
class PdfControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new PdfController();
		//Auth Service
		$sv =  $sm->get ('Application\Service\PdfService' );
		$controller->setPdfService($sv );
		
		// Purchase Request table
		$tbl =  $sm->get ('Procurement\Model\PurchaseRequestTable' );
		$controller->setPrTable($tbl);
		
		// DeliveryTable table
		$tbl =  $sm->get ('Procurement\Model\DeliveryTable' );
		$controller->setDnTable($tbl);
		
		// Delivery Item table
		$tbl =  $sm->get ('Procurement\Model\DeliveryItemTable' );
		$controller->setDnItemTable($tbl);
		
		// Delivery Item table
		$tbl =  $sm->get ('Procurement\Model\PurchaseRequestItemTable' );
		$controller->setPrItemTable($tbl);
		
		// setAssetCountingItemTable
		$tbl =  $sm->get ('Inventory\Model\AssetCountingItemTable' );
		$controller->setAssetCountingItemTable($tbl);
		
		// setAssetCountingItemTable
		$tbl =  $sm->get ('Inventory\Model\AssetPictureTable' );
		$controller->setAssetPictureTable($tbl);
	
		return $controller;
	}
}