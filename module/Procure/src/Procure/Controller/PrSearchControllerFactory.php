<?php
namespace Procure\Controller;

use Procure\Application\Service\Search\ZendSearch\PR\PrSearchQueryImpl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/*
 * @author nmt
 *
 */
class PrSearchControllerFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();

        $controller = new PrSearchController();

        $sv = $sm->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        // Vendor Search Service
        $sv = $sm->get('Procure\Service\PrSearchService');
        $controller->setPrSearchService($sv);

        $sv = $sm->get(PrSearchQueryImpl::class);
        $controller->setQueryService($sv);

        return $controller;
    }
}