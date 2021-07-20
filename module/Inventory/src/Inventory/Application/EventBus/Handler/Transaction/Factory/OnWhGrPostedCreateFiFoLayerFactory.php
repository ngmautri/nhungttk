<?php
namespace Inventory\Application\EventBus\Handler\Transaction\Factory;

use Inventory\Application\EventBus\EventBusService;
use Inventory\Application\EventBus\Handler\Transaction\OnWhGrPostedCreateFiFoLayer;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnWhGrPostedCreateFiFoLayerFactory implements FactoryInterface
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

        $service = new OnWhGrPostedCreateFiFoLayer($doctrineEM, $eventBusService);

        $sv = $container->get("AppLogger");
        $service->setLogger($sv);

        $sv = $container->get("AppCache");
        $service->setCache($sv);

        return $service;
    }
}