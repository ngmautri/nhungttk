<?php

namespace Application\Controller\Plugin;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * 
 * @author nmt
 *
 */
class NmtPluginFactory implements FactoryInterface
{
    /**
     *
     * {@inheritDoc}
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {
        
        $container= $serviceLocator->getServiceLocator();
        
        $p= new NmtPlugin();
        $sv =  $container->get('doctrine.entitymanager.orm_default');
        $p->setDoctrineEM($sv);        
        return $p;
    }	
}