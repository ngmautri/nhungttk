<?php
namespace Inventory\Application\Service\Upload\Item;

use Inventory\Application\EventBus\EventBusService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UploadItemFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new UploadItem();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);

        $sv = $container->get('InventoryLogger');
        $service->setLogger($sv);

        $sv = $container->get('AppCache');
        $service->setCache($sv);

        $sv = $container->get(EventBusService::class);
        $service->setEventBusService($sv);

        return $service;
    }
}