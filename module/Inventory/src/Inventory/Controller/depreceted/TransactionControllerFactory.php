<?php

namespace Inventory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Controller\TransactionController;

/*
 * @author nmt
 *
 */
class TransactionControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new TransactionController ();
		$tbl =  $sm->get ('Inventory\Model\SparepartCategoryTable' );
		$controller->setSparePartCategoryTable ( $tbl );
		
		$tbl =  $sm->get ('Inventory\Model\SparepartCategoryMemberTable' );
		$controller->setSparePartCategoryMemberTable( $tbl );
		
		$tbl =  $sm->get ('Inventory\Model\MLASparepartTable' );
		$controller->setSparepartTable( $tbl );
		
		$tbl =  $sm->get ('Inventory\Model\ArticleCategoryTable' );
		$controller->setArticleCategoryTable( $tbl );
		
		$tbl =  $sm->get ('Inventory\Model\ArticleCategoryMemberTable' );
		$controller->setArticleCategoryMemberTable( $tbl );
		
		$tbl =  $sm->get ('Inventory\Model\ArticleTable' );
		$controller->setArticleTable( $tbl );
		
		//Warehouse Table
		$tbl =  $sm->get ('Inventory\Model\WarehouseTable' );
		$controller->setWhTable( $tbl );
		
		//Article Movemebt Table
		$tbl =  $sm->get ('Inventory\Model\ArticleMovementTable' );
		$controller->setArticleMovementTable( $tbl );
		
		//Article Movemebt Table
		$tbl =  $sm->get ('Inventory\Model\ArticlePictureTable' );
		$controller->setArticlePictureTable( $tbl );
		
		
		//User Table
		$tbl =  $sm->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		//Auth Service
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
		return $controller;
	}
}