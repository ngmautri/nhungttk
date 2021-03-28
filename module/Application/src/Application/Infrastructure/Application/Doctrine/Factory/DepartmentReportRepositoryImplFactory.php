<?php
namespace Application\Infrastructure\Persistence\Application\Doctrine\Factory;

use Application\Infrastructure\Persistence\Application\Doctrine\DepartmentReportRepositoryImpl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
class DepartmentReportRepositoryImplFactory implements FactoryInterface
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

        $service = new DepartmentReportRepositoryImpl($sv);
        return $service;
    }
}