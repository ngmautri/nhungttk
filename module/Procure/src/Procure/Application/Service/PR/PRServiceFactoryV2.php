<?php
namespace Procure\Application\Service\PR;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRServiceFactoryV2 implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new PRServiceV2();

        $sv = $container->get('ControllerPluginManager');
        $service->setControllerPlugin($sv->get('NmtPlugin'));

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);

        $sv = $container->get("AppCache");
        $service->setCache($sv);

        $sv = $container->get("AppLogger");
        $service->setLogger($sv);

        $sv = $container->get("translator");
        $service->setTranslator($sv);

        return $service;
    }
}