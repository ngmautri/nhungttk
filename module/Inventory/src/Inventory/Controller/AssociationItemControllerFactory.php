<?php
namespace Inventory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class AssociationItemControllerFactory implements FactoryInterface
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

        $controller = new AssociationItemController();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $container->get('AppLogger');
        $controller->setLogger($sv);

        return $controller;
    }
}