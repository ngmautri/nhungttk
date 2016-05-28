<?php
namespace Application\Service;

use Zend\Permissions\Acl\Acl;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Controller\ControllerManager;
use MLA\Service\AbstractService;


/*
 * @author nmt
 *
 */
class ApplicationService extends AbstractService
{	
	protected $moduleManager;
	protected $controllerManager;
	
	
	public function initAcl(Acl $acl){
		// TODO
	}
	
	public function getLoadedModules(){
		$loadedModules = $this->moduleManager->getLoadedModules();
		
		$modules = array();
		$controllers = $this->getRegisteredControllers();
		foreach($loadedModules as $key=>$value){
			var_dump($value);
			
			
			$con_array = array();
			foreach($controllers as $c){
				
				if( $c[0]===$key){
					//get Action
					$controller_cls =  $c[0]."\\".$c[1]."\\".$c[2];
					$actions = $this->getActions($controller_cls);
					$c1 = array(
						"Controller"=> $c[2],
						"Actions" =>$actions,
					);
					$con_array[] = $c1 ;
				}
			}
			
			$c = array(
					"Module"=> $key,
					"Controller" =>$con_array,
			);
			$modules[] = $c;
		}
		return $modules;
	}
	
	
	public function getResources(){
		$registerControllers =   $this->controllerManager->getCanonicalNames();
		
		$resources = array();
		foreach ($registerControllers as $key => $value)
		{
			$actions = $this->getActions($key);
						
			foreach ($actions as $action){
				$resources[] = $key .'-'.$action;
			}
			//$resources[] = $c;
		}
		return $resources;
	}
	
	
	public function getRegisteredControllers(){
		$registerControllers =   $this->controllerManager->getCanonicalNames();
		//var_dump($registerControllers);
		
		$data = array();
		
		foreach ($registerControllers as $key => $value)
		{
			$data[]=explode("\\",$key);
		}
		return $data;
	}

	public function getActions($controller_cls){
		
		//$controller = $this->controllerManager->get("Application\Controller\Department");
		$controller = $this->controllerManager->get($controller_cls);
		
		$r = new \ReflectionClass($controller);
		$actions = array();
		
		foreach($r->getMethods() as $m){
			$methodName = $m->getName();
			
			if ($methodName == 'getMethodFromAction') {
				continue;
			}
			
			if(substr($methodName,strlen($methodName)-6,6) ==='Action'):
					$actions[] = substr($methodName,0,strlen($methodName)-6);
			endif;
			//$actions[] = $methodName;
		}
			
		return $actions;
	}
	
	
	public function getModuleManager() {
		return $this->moduleManager;
	}
	public function setModuleManager(ModuleManager $moduleManager) {
		$this->moduleManager = $moduleManager;
		return $this;
	}
	public function getControllerManager() {
		return $this->controllerManager;
	}
	public function setControllerManager(ControllerManager $controllerManager) {
		$this->controllerManager = $controllerManager;
		return $this;
	}
	
	
	
}