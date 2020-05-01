<?php
namespace Procure\Controller;

use Procure\Application\Reporting\PO\PoReporter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoReportControllerFactory implements FactoryInterface
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
        $controller = new PoReportController();

        $sv = $sm->get("AppLogger");
        $controller->setLogger($sv);

        $sv = $sm->get(PoReporter::class);
        $controller->setReporter($sv);

        return $controller;
    }
}