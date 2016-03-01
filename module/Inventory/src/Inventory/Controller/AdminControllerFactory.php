<?php

namespace Inventory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Controller\AdminController;

/*
 * @author nmt
 *
 */
class AdminControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new AdminController ();
		$tbl =  $sm->get ('Inventory\Model\SparepartCategoryTable' );
		$controller->setSparePartCategoryTable ( $tbl );
		
		$tbl =  $sm->get ('Inventory\Model\SparepartCategoryMemberTable' );
		$controller->setSparePartCategoryMemberTable( $tbl );
		
		$tbl =  $sm->get ('Inventory\Model\MLASparepartTable' );
		$controller->setSparepartTable( $tbl );
		
		return $controller;
	}
}