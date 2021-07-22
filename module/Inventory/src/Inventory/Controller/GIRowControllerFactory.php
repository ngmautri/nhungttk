<?php
namespace Inventory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GIRowControllerFactory implements FactoryInterface
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

        $controller = new GIRowController();

        $sv = $sm->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $sm->get('Inventory\Service\GIService');
        $controller->setGiService($sv);

        $sv = $sm->get('Inventory\Service\Report\ItemReportService');
        $controller->setItemReportService($sv);

        $sv = $sm->get('Inventory\Service\InventoryTransactionService');
        $controller->setInventoryTransactionService($sv);

        $sv = $sm->get('Inventory\Application\Service\Warehouse\TransactionService');

        $controller->setTransactionService($sv);

        return $controller;
    }
}