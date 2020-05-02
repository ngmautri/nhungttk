<?php
namespace Procure\Application\Reporting\GR;

use Procure\Infrastructure\Persistence\Doctrine\GrReportRepositoryImpl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrReporterFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new GrReporter();

        $sv = $container->get('ControllerPluginManager');
        $service->setControllerPlugin($sv->get('NmtPlugin'));

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);

        $sv = $container->get(GrReportRepositoryImpl::class);
        $service->setReporterRespository($sv);

        $sv = $container->get("AppCache");
        $service->setCache($sv);

        return $service;
    }
}