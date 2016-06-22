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
		
		
		return $controller;
	}
}