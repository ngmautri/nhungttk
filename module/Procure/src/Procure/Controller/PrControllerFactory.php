<?php
namespace Procure\Controller;

use Procure\Application\Reporting\PR\PrReporter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author nmt
 *        
 */
class PrControllerFactory implements FactoryInterface
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

        $controller = new PrController();

        $sv = $sm->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $sm->get('Application\Service\PdfService');
        $controller->setPdfService($sv);

        $sv = $sm->get('Procure\Service\PrService');
        $controller->setPrService($sv);

        $sv = $sm->get('Procure\Application\Service\PR\PRService');
        $controller->setPurchaseRequestService($sv);

        $sv = $sm->get("AppLogger");
        $controller->setLogger($sv);

        $sv = $sm->get(PrReporter::class);
        $controller->setPrReporter($sv);

        return $controller;
    }
}