<?php
namespace Inventory\Controller;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DashboardControllerFactory implements FactoryInterface
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

        $controller = new DashboardController();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $container->get('Inventory\Infrastructure\Persistence\DoctrineItemReportingRepository');
        $controller->setItemReportingRepository($sv);

        $sv = $container->get('Inventory\Infrastructure\Persistence\DoctrineItemListRepository');
        $controller->setItemListRepository($sv);

        return $controller;
    }
}