<?php

namespace Application\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Service\PdfService;

/*
 * @author nmt
 *
 */
class PdfServiceFactory implements FactoryInterface {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		
			
		$s = new PdfService();
		
		$sv =  $serviceLocator->get ('ModuleManager' );
		$s->setModuleManager($sv);
		
		$sv =  $serviceLocator->get ('ControllerLoader' );
		$s->setControllerManager($sv);
		return $s;
	}
}