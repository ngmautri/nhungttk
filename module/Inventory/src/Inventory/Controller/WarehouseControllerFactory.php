<?php
namespace Inventory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Controller\WarehouseController;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseControllerFactory implements FactoryInterface
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

        $controller = new WarehouseController();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $container->get('\Inventory\Service\WarehouseService');
        $controller->setWarehouseService($sv);

        $sv = $container->get('\Inventory\Service\WarehouseLocationService');
        $controller->setWarehouseLocationService($sv);

        $sv = $container->get('\Inventory\Application\Service\Warehouse\WarehouseService');
        $controller->setWhService($sv);

        return $controller;
    }
}