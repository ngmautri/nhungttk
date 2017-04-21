<?php

namespace Inventory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Controller\SearchController;

/*
 * @author nmt
 *
 */
class SearchControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new SearchController ();
		
		//User Table
		$tbl =  $sm->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		
		// Purchase Request Cart Item table
		$tbl =  $sm->get ('Procurement\Model\PurchaseRequestCartItemTable' );
		$controller->setPurchaseRequestCartItemTable($tbl );
		
		
		
		//Article Search Service
		$sv =  $sm->get ('Inventory\Services\ArticleSearchService' );
		$controller->setArticleSearchService($sv );
	
		//Asset Search Service
		$sv =  $sm->get ('Inventory\Services\AssetSearchService' );
		$controller->setAssetSearchService($sv );

		//Spare-Part Search Service
		$sv =  $sm->get ('Inventory\Services\SparePartsSearchService' );
		$controller->setSparePartSearchService($sv );
		
		//Spare-Part Search Service
		$sv =  $sm->get ('Inventory\Service\ItemSearchService' );
		$controller->setItemSearchService($sv );
		
		//Auth Service
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
		
		
		return $controller;
	}
}