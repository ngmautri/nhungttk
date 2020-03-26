<?php
namespace Inventory\Application\Service\Warehouse;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseServiceFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new WarehouseService();

        $sv = $container->get('ControllerPluginManager');
        $service->setControllerPlugin($sv->get('NmtPlugin'));

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);

        $eventManager = $container->get('EventManager');

        /*
         * $l = $container->get('Inventory\Application\Event\Listener\ItemCreatedEventListener');
         * $eventManager->attachAggregate($l);
         *
         * $l = $container->get('Inventory\Application\Event\Listener\ItemUpdatedEventListener');
         * $eventManager->attachAggregate($l);
         */
        $l = $container->get('Inventory\Application\Event\Listener\WarehouseLoggingListener');
        $eventManager->attachAggregate($l);

        $service->setEventManager($eventManager);

        return $service;
    }
}