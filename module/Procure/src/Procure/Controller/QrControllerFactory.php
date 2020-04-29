<?php
namespace Procure\Controller;

use Procure\Application\Eventbus\EventBusService;
use Procure\Application\Reporting\QR\QrReporter;
use Procure\Application\Service\QR\QRService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QrControllerFactory implements FactoryInterface
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
        $controller = new QrController();
        $sv = $sm->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $sm->get(QRService::class);
        $controller->setQrService($sv);

        $sv = $sm->get(EventBusService::class);
        $controller->setEventBusService($sv);

        $sv = $sm->get(QrReporter::class);
        $controller->setQrReporter($sv);

        $sv = $sm->get("AppLogger");
        $controller->setLogger($sv);

        return $controller;
    }
}