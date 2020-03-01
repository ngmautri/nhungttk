<?php

namespace Inventory\Application\Service\Item;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemCategoryServiceFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		$container = $serviceLocator;
		
		$service = new ItemCategoryService();
		
		$sv =  $container->get('ControllerPluginManager');
		$service->setControllerPlugin($sv->get('NmtPlugin'));
		
		$sv =  $container->get('Inventory\Infrastructure\Persistence\Doctrine\ItemCategoryRepositoryImpl');
		$service->setItemCategoryRepository($sv);
		
		return $service;
	}
}