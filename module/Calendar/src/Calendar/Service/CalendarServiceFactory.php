<?php

namespace Calendar\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/*
 * @author nmt
 *
 */
class CalendarServiceFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
		//$container = $serviceLocator;
		
		$service = new CalendarService();
		
	/* 	$sv =  $container->get('doctrine.entitymanager.orm_default');
		$service->setDoctrineEM($sv); */
		return $service;
	}
}