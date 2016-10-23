<?php

namespace Inventory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Controller\PurchasingController;

/*
 * @author nmt
 *
 */
class PurchasingControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new PurchasingController();
		
		//User Table
		$tbl =  $sm->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		//Article Table
		$tbl =  $sm->get ('Inventory\Model\ArticleTable' );
		$controller->setArticleTable( $tbl );
		//Article Table
		$tbl =  $sm->get ('Inventory\Model\MLASparepartTable' );
		$controller->setSparePartTable( $tbl );
		
		//ArticlePurchasing Table
		$tbl =  $sm->get ('Inventory\Model\ArticlePurchasingTable' );
		$controller->setArticlePurchasingTable( $tbl );
		
		//ArticlePurchasing Table
		$tbl =  $sm->get ('Inventory\Model\SparepartPurchasingTable' );
		$controller->setSpPurchasingTable( $tbl );
		
		
		// Department table
		$tbl =  $sm->get ('Application\Model\DepartmentTable' );
		$controller->setDepartmentTable($tbl );
			
		//Auth Service
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
		return $controller;
	}
}