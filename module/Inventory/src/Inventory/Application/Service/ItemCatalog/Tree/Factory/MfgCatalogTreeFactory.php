<?php
namespace Inventory\Application\Service\ItemCatalog\Tree\Factory;

use Inventory\Application\Service\ItemCatalog\Tree\ItemCatalogTree;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class ItemCatalogTreeFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new ItemCatalogTree();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);

        return $service;
    }
}