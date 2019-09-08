<?php
namespace Procure\Infrastructure\Doctrine\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Procure\Infrastructure\Doctrine\DoctrinePOCmdRepository;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class POCmdRepositoryFactory implements FactoryInterface
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
        
        $service = new DoctrinePOCmdRepository($sv);
         return $service;
    }
    
}