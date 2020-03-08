<?php
namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class MessageStoreControllerFactory implements FactoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = $serviceLocator->getServiceLocator();

        $controller = new MessageStoreController();

        $sv = $container->get('doctrine.entitymanager.orm_default');
        $controller->setDoctrineEM ( $sv );
		
		$sv = $container->get ( \Application\Application\Service\MessageStore\MessageQuery::class );
		$controller->setMessageQuery( $sv );
		
		return $controller;
	}
}