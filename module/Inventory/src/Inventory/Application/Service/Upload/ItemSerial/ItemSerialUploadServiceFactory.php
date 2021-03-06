<?php
namespace Inventory\Application\Service\Upload;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialUploadServiceFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new ItemSerialUploadService();

        $sv = $container->get('ControllerPluginManager');
        $service->setControllerPlugin($sv->get('NmtPlugin'));

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);

        $eventManager = $container->get('EventManager');

        $grListener = $container->get('Application\Listener\LoggingListener');
        $eventManager->attachAggregate($grListener);

        $grListener = $container->get('Application\Listener\PictureUploadListener');
        $eventManager->attachAggregate($grListener);

        $service->setEventManager($eventManager);

        return $service;
    }
}