<?php
namespace Inventory\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Procure\Controller\PrRowController;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransferRowControllerFactory implements FactoryInterface
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

        $controller = new TransferRowController();

        $sv = $sm->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

     	$sv = $sm->get('Inventory\Service\Report\ItemReportService' );
		$controller->setItemReportService($sv );
		
		$sv =  $sm->get('Inventory\Service\InventoryTransactionService');
		$controller->setInventoryTransactionService($sv);
		
			return $controller;
	}
}