<?php
namespace Application\Application\EventBus\Handler\Department\Factory;

use Application\Application\EventBus\EventBusService;
use Application\Application\EventBus\Handler\Department\OnDepartmentRenamedSaveToLog;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnDepartmentRenamedSaveToLogFactory implements FactoryInterface
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

        $service = new OnDepartmentRenamedSaveToLog($doctrineEM, $eventBusService);

        $sv = $container->get("AppLogger");
        $service->setLogger($sv);

        $sv = $container->get("AppCache");
        $service->setCache($sv);

        return $service;
    }
}