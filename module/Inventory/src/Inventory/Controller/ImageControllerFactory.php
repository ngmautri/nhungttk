<?php

namespace Inventory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Controller\ImageController;

/*
 * @author nmt
 *
 */
class ImageControllerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$sm = $serviceLocator->getServiceLocator();
			
		$controller = new ImageController();
		
		
		$tbl =  $sm->get ('User\Model\UserTable' );
		$controller->setUserTable($tbl);
		
		
		$tbl =  $sm->get ('Inventory\Model\AssetPictureTable' );
		$controller->setAssetPictureTable( $tbl );
		
		$tbl =  $sm->get ('Inventory\Model\SparepartPictureTable' );
		$controller->setSparepartPictureTable( $tbl );
		
		$tbl =  $sm->get ('Inventory\Model\ArticlePictureTable' );
		$controller->setArticlePictureTable($tbl );
				
		//Auth Service
		$sv =  $sm->get ('AuthService' );
		$controller->setAuthService($sv );
		
		return $controller;
	}
}