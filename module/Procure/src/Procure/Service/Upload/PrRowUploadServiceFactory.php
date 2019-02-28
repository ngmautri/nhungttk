<?php

namespace Procure\Service\Upload;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Procure\Service\Upload\PrRowUploadService;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PrRowUploadServiceFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container = $serviceLocator;
		
		$service = new PrRowUploadService();
		
		$sv =  $container->get('ControllerPluginManager');
		$service->setControllerPlugin($sv->get('NmtPlugin'));
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$service->setDoctrineEM($sv);
		
		$eventManager =  $container->get('EventManager');
		
		$grListener = $container->get('Application\Listener\LoggingListener');			
		$eventManager->attachAggregate($grListener);
		
		$grListener = $container->get('Application\Listener\PictureUploadListener');		
		$eventManager->attachAggregate($grListener);
		
		$service->setEventManager($eventManager);
		
		return $service;
	}
}