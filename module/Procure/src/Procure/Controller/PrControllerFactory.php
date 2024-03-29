<?php
namespace Procure\Controller;

use Application\Application\Service\Shared\DefaultFormOptionCollection;
use Procure\Application\Command\Doctrine\PR\Factory\PRCmdHandlerFactory;
use Procure\Application\EventBus\EventBusService;
use Procure\Application\Service\PR\PRService;
use Procure\Application\Service\PR\PRServiceV2;
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

        $sv = $sm->get(PRServiceV2::class);
        $controller->setProcureServiceV2($sv);

        $controller->setCmdHandlerFactory(new PRCmdHandlerFactory());

        $sv = $sm->get(DefaultFormOptionCollection::class);
        $controller->setFormOptionCollection($sv);

        $sv = $sm->get("ProcureLogger");
        $controller->setLogger($sv);

        return $controller;
    }
}