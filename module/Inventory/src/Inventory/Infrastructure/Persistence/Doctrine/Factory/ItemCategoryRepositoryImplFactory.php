<?php
namespace Inventory\Infrastructure\Persistence\Doctrine\Factory;

use Inventory\Infrastructure\Persistence\Doctrine\ItemCategoryRepositoryImpl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class ItemCategoryRepositoryImplFactory implements FactoryInterface
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

        $service = new ItemCategoryRepositoryImpl($sv);
        return $service;
    }
}