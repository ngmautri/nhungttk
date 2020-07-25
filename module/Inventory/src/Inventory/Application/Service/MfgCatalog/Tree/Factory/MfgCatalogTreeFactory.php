<?php
namespace Inventory\Application\Service\MfgCatalog\Tree\Factory;

use Inventory\Application\Service\MfgCatalog\Tree\MfgCatalogTree;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class MfgCatalogTreeFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new MfgCatalogTree();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);

        return $service;
    }
}