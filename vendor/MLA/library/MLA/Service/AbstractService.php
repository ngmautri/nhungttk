<?php
namespace MLA\Service;
use Zend\Permissions\Acl\Acl;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author Ngmautri
 * service is register in Servicemanager, can have Service locator
 *
 */
class AbstractService implements ServiceLocatorAwareInterface

{
	public function initAcl(Acl $acl){
		// TODO
	}
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator){
		
	}
	
	/**
	 * Get service locator
	 *
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator(){
		
	}
	
	
}