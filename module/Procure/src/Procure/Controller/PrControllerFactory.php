<?php
namespace Procure\Controller;

use Procure\Application\Command\Doctrine\PR\Factory\PRCmdHandlerFactory;
use Procure\Application\EventBus\EventBusService;
use Procure\Application\Service\PR\PRService;
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

        $sv = $sm->get(EventBusService::class);
        $controller->setEventBusService($sv);

        $sv = $sm->get(PRService::class);
        $controller->setProcureService($sv);

        $controller->setCmdHandlerFactory(new PRCmdHandlerFactory());

        $sv = $sm->get("ProcureLogger");
        $controller->setLogger($sv);

        return $controller;
    }
}