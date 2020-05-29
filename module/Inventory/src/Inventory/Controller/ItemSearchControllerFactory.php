<?php
namespace Inventory\Controller;

use Inventory\Application\Service\Search\ZendSearch\Item\ItemSearchQueryImpl;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSearchControllerFactory implements FactoryInterface
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

        $controller = new ItemSearchController();

        $sv = $sm->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $sm->get('Inventory\Service\ItemSearchService');
        $controller->setItemSearchService($sv);

        $sv = $sm->get(ItemSearchQueryImpl::class);
        $controller->setItemQueryService($sv);

        return $controller;
    }
}