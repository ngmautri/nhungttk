<?php
namespace Procure\Controller;

use Procure\Application\Eventbus\EventBusService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Procure\Application\Service\PO\POService;
use Procure\Application\Command\Doctrine\PO\Factory\POCmdHandlerFactory;

/**
 *
 * @author nmt
 *
 */
class PoControllerFactory implements FactoryInterface
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

        $controller = new PoController();

        $sv = $sm->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $sm->get(EventBusService::class);
        $controller->setEventBusService($sv);

        $sv = $sm->get(POService::class);
        $controller->setProcureService($sv);

        $controller->setCmdHandlerFactory(new POCmdHandlerFactory());

        $sv = $sm->get("ProcureLogger");
        $controller->setLogger($sv);

        return $controller;
    }
}