<?php

namespace Application\Service;


use Application\Service\CategoryService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

/*
 * @author nmt
 *
 */
class CategoryServiceFactory implements FactoryInterface {
	
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\ServiceManager\Factory\FactoryInterface::__invoke()
	 */
	public function __invoke(ContainerInterface $container,
			$requestedName, array $options = null)
	{	
	
		$service = new AclService();
		
		/* $tbl =  $container->get ('User\Model\AclResourceTable' );
		$service->setAclResourceTable($tbl);

		$tbl =  $container->get ('User\Model\AclRoleTable' );
		$service->setAclRoleTable($tbl); */
		
		$sv =  $container->get('doctrine.entitymanager.orm_default');
		$service->setDoctrineEM($sv);
		
		return $service;
	}
}