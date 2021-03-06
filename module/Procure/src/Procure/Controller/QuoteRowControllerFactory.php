<?php
namespace Procure\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Procure\Controller\PrRowController;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QuoteRowControllerFactory implements FactoryInterface
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

        $controller = new QuoteRowController();

        $sv = $sm->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM($sv);

        $sv = $sm->get('Procure\Service\QoService' );
		$controller->setQoService($sv );
		return $controller;
	}
}