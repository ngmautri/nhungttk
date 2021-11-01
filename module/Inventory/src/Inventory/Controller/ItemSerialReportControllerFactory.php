<?php
namespace Procure\Controller;

use Procure\Application\Reporting\PR\PrReporter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialReportControllerFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();
        $controller = new PrReportController();

        $sv = $sm->get("AppLogger");
        $controller->setLogger($sv);

        $sv = $sm->get("AppCache");
        $controller->setCache($sv);

        $sv = $sm->get(PrReporter::class);
        $controller->setPrReporter($sv);

        return $controller;
    }
}