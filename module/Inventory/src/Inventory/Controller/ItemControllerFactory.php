<?php
namespace Inventory\Controller;

use Inventory\Application\EventBus\EventBusService;
use Inventory\Application\Service\Item\ItemService;
use Inventory\Application\Service\Upload\Item\UploadItem;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class ItemControllerFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator->getServiceLocator();

        $controller = new ItemController();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $container->get('Inventory\Application\Service\Item\ItemCRUDService');
        $controller->setItemCRUDService($sv);

        $sv = $container->get('Inventory\Service\Report\ItemReportService');
        $controller->setItemReportService($sv);

        $sv = $container->get('Inventory\Infrastructure\Persistence\DoctrineItemReportingRepository');
        $controller->setItemReportingRepository($sv);

        $sv = $container->get('Inventory\Infrastructure\Persistence\DoctrineItemListRepository');
        $controller->setItemListRepository($sv);

        $sv = $container->get('FileSystemCache');
        $controller->setCacheService($sv);

        $sv = $container->get('InventoryLogger');
        $controller->setLogger($sv);

        $sv = $container->get(ItemService::class);
        $controller->setItemService($sv);

        $sv = $container->get(EventBusService::class);
        $controller->setEventBusService($sv);
        
        $sv = $container->get(UploadItem::class);
        $controller->setItemUploadService($sv);
        
        return $controller;
    }
}