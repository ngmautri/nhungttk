<?php
namespace Application\Controller;

use Application\Controller\ItemCategoryController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/*
 * @author nmt
 *
 */
class ItemCategoryControllerFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator->getServiceLocator();

        $controller = new ItemCategoryController();

        // Auth Service
        $sv = $container->get('AuthService');
        $controller->setAuthService($sv);

        // Auth Service
        $sv = $container->get('Application\Service\ItemCategoryService');
        $controller->setItemCategoryService($sv);

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        return $controller;
    }
}