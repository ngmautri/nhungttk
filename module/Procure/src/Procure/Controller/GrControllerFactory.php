<?php
namespace Procure\Controller;

use Procure\Application\Command\Doctrine\GR\Factory\GRCmdHandlerFactory;
use Procure\Application\EventBus\EventBusService;
use Procure\Application\Reporting\GR\GrReporter;
use Procure\Application\Service\GR\GRService;
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

        $sv = $sm->get(EventBusService::class);
        $controller->setEventBusService($sv);

        $sv = $sm->get(GRService::class);
        $controller->setProcureService($sv);

        $controller->setCmdHandlerFactory(new GRCmdHandlerFactory());

        $sv = $sm->get("ProcureLogger");
        $controller->setLogger($sv);

        $sv = $sm->get(GrReporter::class);
        $controller->setGrReporter($sv);

        return $controller;
    }
}