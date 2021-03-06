<?php

namespace Procure\Controller;

use Procure\Controller\PrAttachmentController;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * 
 * @author nmt
 *
 */
class PrAttachmentControllerFactory implements FactoryInterface{
	
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container= $serviceLocator->getServiceLocator();
		
		
		$controller= new PrAttachmentController();
		
		//attach PictureUploadListener.
		/* $pictureUploadListener = $container->get('Application\Listener\PictureUploadListener');
		$controller->getEventManager()->attachAggregate($pictureUploadListener);
		 */
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$controller->setDoctrineEM($sv);
		
		$sv =  $container->get('Procure\Service\Upload\PrUploadService');
		$controller->setAttachmentService($sv);
		
		
		return $controller;
	}	
	
}