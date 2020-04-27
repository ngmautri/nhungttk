<?php
namespace Procure\Controller;

use Procure\Application\Eventbus\EventBusService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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

        $sv = $sm->get('Procure\Service\PoService');
        $controller->setPoService($sv);

        $sv = $sm->get('Procure\Service\PoSearchService');
        $controller->setPoSearchService($sv);

        $sv = $sm->get('Procure\Application\Service\PO\POService');
        $controller->setPurchaseOrderService($sv);

        $sv = $sm->get('Procure\Application\Reporting\PO\PoReporter');
        $controller->setPoReporter($sv);

        $sv = $sm->get(EventBusService::class);
        $controller->setEventBusService($sv);

        $sv = $sm->get("AppLogger");
        $controller->setLogger($sv);

        return $controller;
    }
}