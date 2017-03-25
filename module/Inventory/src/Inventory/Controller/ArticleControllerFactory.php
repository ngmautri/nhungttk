<?php

namespace Inventory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Controller\ArticleController;

/*
 * @author nmt
 *
 */
class ArticleControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new ArticleController();
		
		//User Table
		$tbl =  $sm->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		//Article Table
		$tbl =  $sm->get ('Inventory\Model\ArticleTable' );
		$controller->setArticleTable( $tbl );

		//Article Picture Table
		$tbl =  $sm->get ('Inventory\Model\ArticlePictureTable' );
		$controller->setArticlePictureTable($tbl );
		
		//Article Category Table
		$tbl =  $sm->get ('Inventory\Model\ArticleCategoryTable' );
		$controller->setArticleCategoryTable($tbl );
		
		
		//Article Category Member Table
		$tbl =  $sm->get ('Inventory\Model\ArticleCategoryMemberTable' );
		$controller->setArticleCategoryMemberTable($tbl );

		//mla_article_table
		$tbl =  $sm->get ('Inventory\Model\MlaArticleDepartmentTable' );
		$controller->setMlaArticleDepartmentTable($tbl );
		
		//Article Category Member Table
		$tbl =  $sm->get ('Inventory\Model\ArticleMovementTable' );
		$controller->setArticleMovementTable($tbl );
		
		// Purchase Request Cart Item table
		$tbl =  $sm->get ('Procurement\Model\PurchaseRequestCartItemTable' );
		$controller->setPurchaseRequestCartItemTable($tbl );
	
		// Department table
		$tbl =  $sm->get ('Application\Model\DepartmentTable' );
		$controller->setDepartmentTable($tbl );
		
			
		//Auth Service
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
		//Article Service
		$sv =  $sm->get ('Inventory\Services\ArticleService' );
		$controller->setArticleService($sv );
	
		//Article Search Service
		$sv =  $sm->get ('Inventory\Services\ArticleSearchService' );
		$controller->setArticleSearchService($sv );
		
		//Article Service
		$sv =  $sm->get ('User\Service\ArticleCategory' );
		$controller->setArticleCategoryService($sv );
		
		// sv =  $sm->get ('doctrine.entitymanager.orm_default' );
		// $controller->setDoctrineEM($sv );
		
		return $controller;
	}
}