<?php
namespace Procure\Controller;

use Procure\Application\Reporting\GR\GrReporter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GrControllerFactory implements FactoryInterface
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

        $controller = new GrController();

        $sv = $sm->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $sm->get('Procure\Service\GrService');
        $controller->setGrService($sv);

        $sv = $sm->get('Procure\Application\Service\GR\GRService');
        $controller->setGoodsReceiptService($sv);

        $sv = $sm->get("AppLogger");
        $controller->setLogger($sv);

        $sv = $sm->get(GrReporter::class);
        $controller->setGrReporter($sv);

        return $controller;
    }
}