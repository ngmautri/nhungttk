<?php
namespace Inventory\Controller;

use Inventory\Application\Service\HSCode\HSCodeTreeService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class HSCodeControllerFactory implements FactoryInterface
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

        $controller = new HSCodeController();

        $sv = $container->get(HSCodeTreeService::class);
        $controller->setHsCodeTreeService($sv);

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $container->get('AppLogger');
        $controller->setLogger($sv);

        return $controller;
    }
}