<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\I18n\Validator\Int;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use MLA\Files;

use Application\Service\ApplicationService;
use User\Model\AclResource;
use User\Model\AclResourceTable;


/*
 * Control Panel Controller
 */
class AclController extends AbstractActionController {
	
	protected $authService;
	protected $appService;
	
	protected $userTable;
	protected $aclResourceTable;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	public function denyAction() {
	}
	
	public function listAction() {
		$modules = $this->appService->getLoadedModules();
		return new ViewModel ( array (
				'modules' => $modules,
				'e' => $this->getEvent(),
				
		) );
	}
	
	public function listResourcesAction() {
		

		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 18;
		} else {
			$resultsPerPage = $this->params ()->fromQuery ( 'perPage' );
		}
		;
		
		if (is_null ( $this->params ()->fromQuery ( 'page' ) )) {
			$page = 1;
		} else {
			$page = $this->params ()->fromQuery ( 'page' );
		}
		;
		
		
		$resources = $this->aclResourceTable->getResources(0, 0);
		$totalResults = $resources->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$resources = $this->aclResourceTable->getResources(($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
	
		return new ViewModel ( array (
				'total_resources' => $totalResults,
				'resources' => $resources,
				'paginator' => $paginator,
		) );
	}
	
	public function updateResourcesAction() {
		$resources = $this->appService->getResources();
		foreach($resources as $res){
			
			if(!$this->aclResourceTable->isResourceExits($res))
			{
				$input= new AclResource();
				$input->resource = $res;
				$input->type = "ROUTE";
				$this->aclResourceTable->add($input);
			}
		}
		
		
		
		return new ViewModel ( array (
				'modules' => $modules,
				'e' => $this->getEvent(),
	
		) );
	}
	
	// SETTER AND GETTER
	public function getAuthService() {
		return $this->authService;
	}
	public function setAuthService($authService) {
		$this->authService = $authService;
		return $this;
	}
	public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable($userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getAppService() {
		return $this->appService;
	}
	public function setAppService(ApplicationService $appService) {
		$this->appService = $appService;
		return $this;
	}
	public function getAclResourceTable() {
		return $this->aclResourceTable;
	}
	public function setAclResourceTable(AclResourceTable $aclResourceTable) {
		$this->aclResourceTable = $aclResourceTable;
		return $this;
	}
	
	
	

}
