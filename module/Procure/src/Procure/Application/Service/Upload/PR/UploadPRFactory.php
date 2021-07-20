<?php
namespace Procure\Application\Service\Upload\PR;

use Procure\Application\Eventbus\EventBusService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UploadPRFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new UploadPR();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);

        $sv = $container->get('AppLogger');
        $service->setLogger($sv);

        $sv = $container->get('AppCache');
        $service->setCache($sv);

        $sv = $container->get(EventBusService::class);
        $service->setEventBusService($sv);

        return $service;
    }
}