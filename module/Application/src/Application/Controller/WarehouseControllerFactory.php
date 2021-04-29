<?php
namespace Application\Controller;

use Application\Application\Service\Warehouse\WarehouseService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/*
 * @author nmt
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

        $sv = $container->get(WarehouseService::class);
        $controller->setEntityService($sv);

        $sv = $container->get("AppLogger");
        $controller->setLogger($sv);

        return $controller;
    }
}