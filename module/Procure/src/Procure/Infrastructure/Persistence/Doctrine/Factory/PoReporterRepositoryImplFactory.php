<?php
namespace Procure\Infrastructure\Persistence\Doctrine\Factory;

use Procure\Infrastructure\Persistence\Doctrine\PoReportRepositoryImpl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class PoReporterRepositoryImplFactory implements FactoryInterface
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

        $service = new PoReportRepositoryImpl($sv);
        return $service;
    }
}