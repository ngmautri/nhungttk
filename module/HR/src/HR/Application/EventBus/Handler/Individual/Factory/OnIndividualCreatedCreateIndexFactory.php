<?php
namespace HR\Application\EventBus\Handler\Individual\Factory;

use HR\Application\EventBus\Handler\Individual\OnIndividualCreatedCreateIndex;
use Inventory\Application\EventBus\EventBusService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnIndividualCreatedCreateIndexFactory implements FactoryInterface
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

        $service = new OnIndividualCreatedCreateIndex($doctrineEM, $eventBusService);

        $sv = $container->get("AppLogger");
        $service->setLogger($sv);

        $sv = $container->get("AppCache");
        $service->setCache($sv);

        return $service;
    }
}