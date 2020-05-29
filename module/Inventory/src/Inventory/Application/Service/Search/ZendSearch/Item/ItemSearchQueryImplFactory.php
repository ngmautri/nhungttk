<?php
namespace Inventory\Application\Service\Search\ZendSearch\Item;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class ItemSearchQueryImplFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new ItemSearchQueryImpl();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);
        return $service;
    }
}