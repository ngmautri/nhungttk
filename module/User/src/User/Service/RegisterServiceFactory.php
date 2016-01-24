<?php

namespace User\Service;

use Zend\EventManager\EventManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use User\Service\RegisterService;

/*
 * @author nmt
 *
 */
class RegisterServiceFactory implements FactoryInterface {
	
	
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		// get RegisterListener
		$registerListener = $serviceLocator->get ( 'User\Listener\RegisterListener' );
		
		// get RegisterListener
		$userTable =  $serviceLocator->get ('User\Model\UserTable' );
		
		$eventManager = $serviceLocator->get ( 'EventManager' );
		
		$eventManager->attachAggregate ( $registerListener );
		
		$registerService = new RegisterService();
		$registerService->setEventManager ( $eventManager );
		$registerService->setUserTable($userTable);
		
		return $registerService;
	}
}