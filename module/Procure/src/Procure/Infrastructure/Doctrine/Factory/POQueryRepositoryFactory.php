<?php
namespace Procure\Infrastructure\Doctrine\Factory;

use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class POQueryRepositoryFactory implements FactoryInterface
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
        
        $service = new DoctrinePOQueryRepository($sv);
         return $service;
    }
    
}