<?php
namespace Procure\Infrastructure\Persistence\Doctrine\Factory;

use Procure\Infrastructure\Persistence\Doctrine\ProcureReportRepositoryImpl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class ProcureReporterRepositoryImplFactory implements FactoryInterface
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

        $service = new ProcureReportRepositoryImpl($sv);
        return $service;
    }
}