<?php
namespace Inventory\Controller;

use Inventory\Application\Eventbus\EventBusService;
use Inventory\Application\Service\Transaction\TrxService;
use Inventory\Application\Service\Upload\Transaction\TrxRowsUpload;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRControllerFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator->getServiceLocator();

        $controller = new GRController();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $container->get(TrxService::class);
        $controller->setTrxService($sv);

        $sv = $container->get(EventBusService::class);
        $controller->setEventBusService($sv);

        $sv = $container->get("AppLogger");
        $controller->setLogger($sv);

        $sv = $container->get(TrxRowsUpload::class);
        $controller->setTrxUploadService($sv);

        return $controller;
    }
}