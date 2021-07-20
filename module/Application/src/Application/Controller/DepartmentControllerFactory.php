<?php
namespace Application\Controller;

use Application\Application\Eventbus\EventBusService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentControllerFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator->getServiceLocator();

        $controller = new DepartmentController();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $container->get('Application\Application\Eventbus\EventBusService');
        $controller->setEventBusService($sv);

        $sv = $container->get("AppLogger");
        $controller->setLogger($sv);

        return $controller;
    }
}