<?php

namespace Inventory\Application\Service\Factory;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Application\Service\ItemService;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemServiceFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container = $serviceLocator;
		
		$service = new ItemService();
		
		$sv =  $container->get('ControllerPluginManager');
		$service->setControllerPlugin($sv->get('NmtPlugin'));
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$service->setDoctrineEM($sv);
		
		$grListener = $container->get('Application\Listener\LoggingListener');
	
		$eventManager =  $container->get('EventManager');
		$eventManager->attachAggregate($grListener);
		
		$service->setEventManager($eventManager);
		
		return $service;
	}
}