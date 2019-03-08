<?php
namespace Payment\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OutgoingControllerFactory implements FactoryInterface
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

        $controller = new OutgoingController();

        $sv = $sm->get('doctrine.entitymanager.orm_default' );
		$controller->setDoctrineEM($sv );
		
		$sv = $sm->get('Payment\Service\APPaymentService' );
		$controller->setApPaymentService($sv);
		
		
		
		return $controller;
	}
}