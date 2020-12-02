<?php
namespace Procure\Controller;

use Procure\Application\Command\Doctrine\AP\Factory\APCmdHandlerFactory;
use Procure\Application\Eventbus\EventBusService;
use Procure\Application\Service\AP\APService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ApControllerFactory implements FactoryInterface
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

        $controller = new ApController();

        $sv = $sm->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $sm->get(EventBusService::class);
        $controller->setEventBusService($sv);

        $sv = $sm->get(APService::class);
        $controller->setProcureService($sv);

        $controller->setCmdHandlerFactory(new APCmdHandlerFactory());

        $sv = $sm->get("ProcureLogger");
        $controller->setLogger($sv);

        return $controller;
    }
}