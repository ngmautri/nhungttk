<?php

namespace Workflow\Controller\Plugin;

use Workflow\Controller\Plugin\WfPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * 
 * @author nmt
 *
 */
class WfPluginFactory implements FactoryInterface
{
    /**
     *
     * {@inheritDoc}
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {
        
        $container= $serviceLocator->getServiceLocator();
        
        $p= new WfPlugin();
        $p->setServiceManager($container);
        return $p;
    }	
}