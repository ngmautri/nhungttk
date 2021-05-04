<?php
namespace Procure\Infrastructure\Persistence\Reporting\Doctrine\Factory;

use Procure\Infrastructure\Persistence\Reporting\Doctrine\PrGrReportImpl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
class PrGrReportImplFactory implements FactoryInterface
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
        $doctrineEM = $container->get('doctrine.entitymanager.orm_default');

        $service = new PrGrReportImpl($doctrineEM);
        return $service;
    }
}