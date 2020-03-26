<?php
namespace Inventory\Infrastructure\Persistence\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Inventory\Infrastructure\Persistence\DoctrineItemListRepository;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class DoctrineItemListRepositoryFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;
        $sv = $container->get('doctrine.entitymanager.orm_default');

        $service = new DoctrineItemListRepository($sv);

        return $service;
    }
}