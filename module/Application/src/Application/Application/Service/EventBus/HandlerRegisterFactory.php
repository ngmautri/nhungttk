<?php
namespace Application\Application\Service\Eventbus;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class EventBusServiceFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;
        $service = new HandlerRegister();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);

        return $service;
    }
}