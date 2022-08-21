<?php
namespace Inventory\Application\Service\Search\ZendSearch\Individual;

use HR\Application\Service\Search\ZendSearch\Individual\IndividualSearchIndexImpl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class IndividualSearchIndexImplFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator;

        $service = new IndividualSearchIndexImpl();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $service->setDoctrineEM($sv);
        return $service;
    }
}