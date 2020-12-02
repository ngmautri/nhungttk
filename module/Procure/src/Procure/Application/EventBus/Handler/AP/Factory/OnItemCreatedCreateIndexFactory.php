<?php
namespace Inventory\Application\EventBus\Handler\Item\Factory;

use Inventory\Application\EventBus\Handler\Item\OnItemCreatedCreateIndex;
use Inventory\Application\Eventbus\EventBusService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnItemCreatedCreateIndexFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $doctrineEM = $container->get('doctrine.entitymanager.orm_default');
        $eventBusService = $container->get(EventBusService::class);

        $service = new OnItemCreatedCreateIndex($doctrineEM, $eventBusService);

        $sv = $container->get("AppLogger");
        $service->setLogger($sv);

        $sv = $container->get("AppCache");
        $service->setCache($sv);

        return $service;
    }
}