<?php
namespace Inventory\Controller;

use Inventory\Application\Reporting\Transaction\TrxReporter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxReportControllerFactory implements FactoryInterface
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

        $controller = new TrxReportController();

        $sv = $sm->get(TrxReporter::class);
        $controller->setTrxReporter($sv);

        return $controller;
    }
}