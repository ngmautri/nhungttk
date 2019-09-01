<?php
namespace Procure\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Procure\Controller\PrController;

/**
 *
 * @author nmt
 *        
 */
class PoControllerFactory implements FactoryInterface
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

        $controller = new PoController();

        $sv = $sm->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $sm->get('Procure\Service\PoService');
        $controller->setPoService($sv);

        $sv = $sm->get('Procure\Service\PoSearchService');
        $controller->setPoSearchService($sv);

        $sv = $sm->get('Procure\Application\Service\PO\POService' );
		$controller->setPurchaseOrderService($sv );
		
		return $controller;
	}
}