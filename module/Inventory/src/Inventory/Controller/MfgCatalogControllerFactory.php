<?php
namespace Inventory\Controller;

use Inventory\Application\Service\MfgCatalog\Tree\MfgCatalogTree;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class MfgCatalogControllerFactory implements FactoryInterface
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

        $controller = new MfgCatalogController();

        $sv = $container->get(MfgCatalogTree::class);
        $controller->setMfgCatalogTree($sv);

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $container->get('AppLogger');
        $controller->setLogger($sv);

        $sv = $container->get("AppCache");
        $controller->setCache($sv);

        return $controller;
    }
}