<?php
namespace Inventory\Controller;

use Inventory\Controller\IndexController;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 *
 * @author nmt
 *        
 */
class IndexControllerFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        // $container= $serviceLocator->getServiceLocator();
        $controller = new IndexController();
        return $controller;
    }
}