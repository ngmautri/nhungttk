<?php
namespace Inventory\Controller;

use Inventory\Application\Command\Transaction\Doctrine\Factory\TrxCmdHandlerFactory;
use Inventory\Application\EventBus\EventBusService;
use Inventory\Application\Service\Transaction\TrxService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemOpeningBalanceControllerFactory implements FactoryInterface
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

        $controller = new ItemOpeningBalanceController();

        $sv = $sm->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $sm->get(EventBusService::class);
        $controller->setEventBusService($sv);

        $sv = $sm->get(TrxService::class);
        $controller->setTrxService($sv);

        $controller->setCmdHandlerFactory(new TrxCmdHandlerFactory());

        $sv = $sm->get("AppLogger");
        $controller->setLogger($sv);

        return $controller;
    }
}