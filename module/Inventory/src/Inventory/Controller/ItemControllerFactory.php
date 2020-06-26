<?php
namespace Inventory\Controller;

use Inventory\Application\Eventbus\EventBusService;
use Inventory\Application\Service\Item\ItemService;
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

        $sv = $container->get('AppLogger');
        $controller->setLogger($sv);

        $sv = $container->get(ItemService::class);
        $controller->setItemService($sv);

        $sv = $container->get(EventBusService::class);
        $controller->setEventBusService($sv);
        return $controller;
    }
}