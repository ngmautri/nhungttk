<?php
namespace Inventory\Application\Event\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseLoggingListenerFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;
        $listener = new WarehouseLoggingListener();
        $sv = $container->get('doctrine.entitymanager.orm_default');
        $listener->setDoctrineEM($sv);
        return $listener;
    }
}