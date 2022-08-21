<?php
namespace HR\Application\EventBus;

use Application\Application\EventBus\PsrHandlerResolver;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class EventBusServiceFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;
        $service = new EventBusService();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);

        $sv = $container->get(PsrHandlerResolver::class);
        $service->setResolver($sv);

        $sv = $container->get(HandlerMapper::class);
        $service->setMapper($sv);

        $sv = $container->get("AppLogger");
        $service->setLogger($sv);

        return $service;
    }
}