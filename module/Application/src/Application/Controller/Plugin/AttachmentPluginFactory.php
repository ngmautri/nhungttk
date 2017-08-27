<?php

namespace Application\Controller\Plugin;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * 
 * @author nmt
 *
 */
class AttachmentPluginFactory implements FactoryInterface
{
    /**
     *
     * {@inheritDoc}
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {
        
        $container= $serviceLocator->getServiceLocator();
        
        $p= new AttachmentPlugin();
        $p->setServiceManager($container);
        return $p;
    }	
}