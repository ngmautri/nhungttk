<?php
namespace Inventory\Controller;

use Inventory\Controller\ItemTransactionController;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 *
 * @author nmt
 *        
 */
class ItemTransactionControllerFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator->getServiceLocator();

        $controller = new ItemTransactionController();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $container->get('Inventory\Service\ItemSearchService');
        $controller->setItemSearchService($sv);

        $sv = $container->get('Inventory\Service\GIService');
        $controller->setGiService($sv);

        $sv = $container->get('Inventory\Service\FIFOLayerService');
        $controller->setFifoLayerService($sv);

        return $controller;
    }
}