<?php
namespace Procure\Infrastructure\Persistence\Doctrine\Factory;

use Procure\Infrastructure\Persistence\Doctrine\POListRepository;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
class POListRepositoryFactory implements FactoryInterface
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
        
        $controller = new POListRepository();
        
        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);
        return $controller;
    }
    
}