<?php
namespace Inventory\Application\Reporting\Transaction;

use Inventory\Infrastructure\Persistence\Doctrine\TrxReportRepositoryImpl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class StockReporterFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new TrxReporter();

        $sv = $container->get('ControllerPluginManager');
        $service->setControllerPlugin($sv->get('NmtPlugin'));

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);

        $sv = $container->get(TrxReportRepositoryImpl::class);
        $service->setReporterRespository($sv);
        $sv = $container->get("AppCache");
        $service->setCache($sv);

        $sv = $container->get("AppLogger");
        $service->setLogger($sv);

        return $service;
    }
}