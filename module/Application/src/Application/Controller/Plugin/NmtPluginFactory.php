<?php

namespace Application\Controller\Plugin;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
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
        
        $sv =  $container->get('translator');
        $p->setTranslator($sv);
        
        
        $config = $container->get('config');
        $dbConfig = $config['db'];
        $p->setDbConfig($dbConfig);
        
        $sv =  $container->get('Application\Service\SmtpOutlookService');
        $p->setStmpOutlook($sv);
        
        return $p;
    }	
}