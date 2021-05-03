<?php
namespace Procure\Infrastructure\Persistence\Reporting\Doctrine\Factory;

use Procure\Infrastructure\Persistence\Reporting\Doctrine\PoApReportImpl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
class PoApReportImplFactory implements FactoryInterface
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

        $service = new PoApReportImpl($doctrineEM);
        return $service;
    }
}