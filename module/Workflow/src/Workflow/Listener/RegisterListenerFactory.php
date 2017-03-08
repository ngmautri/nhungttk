<?php

namespace User\Listener;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use User\Listener\RegisterListener;

/*
 * @author nmt
 *
 */
class RegisterListenerFactory implements FactoryInterface {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
	
		$mailTransport = $serviceLocator->get ( 'SmtpTransportService' );
		//$orderLog = $serviceLocator->get ( 'Order\Log' );
		// instantiate class
		
		$registerListener = new RegisterListener ();
		$registerListener->setMailTransport ( $mailTransport );
		//$orderListener->setOrderLog ( $orderLog );
		return $registerListener;
	}
}