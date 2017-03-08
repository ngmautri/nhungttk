<?php

namespace User\Utility;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use User\Utility\ArticleCategory;

/*
 * @author nmt
 *
 */
class ArticleCategoryFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$service = new ArticleCategory();
			
		//User Table
		$tbl =  $serviceLocator->get ('Inventory\Model\ArticleCategoryTable' );
		$service->setArticleCategoryTable($tbl);
		
		return $service;
	}
}