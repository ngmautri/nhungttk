<?php
namespace Procure\Application\EventBus\Handler\GR\Factory;

use Procure\Application\EventBus\Handler\GR\OnApPostedCreateGrByWarehouse;
use Procure\Application\Eventbus\EventBusService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class OnApPostedCreateGrByWarehouseFactory implements FactoryInterface
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
        $service = new OnApPostedCreateGrByWarehouse($doctrineEM, $eventBusService);

        $sv = $container->get("AppLogger");
        $service->setLogger($sv);

        return $service;
    }
}