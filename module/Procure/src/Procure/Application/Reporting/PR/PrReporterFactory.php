<?php
namespace Procure\Application\Reporting\PR;

use Procure\Infrastructure\Persistence\Reporting\Doctrine\PrGrReportImpl;
use Procure\Infrastructure\Persistence\Reporting\Doctrine\PrReportImplV1;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrReporterFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new PrReporterV1();

        $sv = $container->get('ControllerPluginManager');
        $service->setControllerPlugin($sv->get('NmtPlugin'));

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);

        // $sv = $container->get("RedisCache");
        $sv = $container->get("AppCache");
        $service->setCache($sv);

        $sv = $container->get(PrReportImplV1::class);
        $service->setReporterRespository($sv);

        $sv = $container->get(PrGrReportImpl::class);
        $service->setPrGrReportRepository($sv);

        return $service;
    }
}