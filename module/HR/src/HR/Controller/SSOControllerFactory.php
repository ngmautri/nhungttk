<?php
namespace HR\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use HR\Controller\IndexController;

/**
 *
 * @author nmt
 *        
 */
class SSOControllerFactory implements FactoryInterface
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
        $controller = new SSOController();
        // $sv = $container->get('doctrine.entitymanager.orm_default');
        // $controller->setDoctrineEM($sv);
        return $controller;
    }
}