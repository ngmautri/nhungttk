<?php

namespace Inventory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Controller\SparepartsController;

/*
 * @author nmt
 *
 */
class SparepartsControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new SparepartsController ();
		
		// Spare Part table
		$tbl =  $sm->get ('Inventory\Model\MLASparepartTable' );
		$controller->setSparePartTable($tbl);
		
		$tbl =  $sm->get ('Inventory\Model\SparepartPictureTable' );
		$controller->setSparePartPictureTable( $tbl );
		
		$tbl =  $sm->get ('Inventory\Model\SparepartMovementsTable' );
		$controller->setSparepartMovementsTable( $tbl );

		$tbl =  $sm->get ('Inventory\Model\SparepartCategoryTable' );
		$controller->setSparePartCategoryTable( $tbl );

		$tbl =  $sm->get ('Inventory\Model\SparepartCategoryMemberTable' );
		$controller->setSparePartCategoryMemberTable( $tbl );
		
		$tbl =  $sm->get ('Inventory\Services\SparepartService' );
		$controller->setSparePartService( $tbl );
		
		
		return $controller;
	}
}