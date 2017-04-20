<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Application\Service\ApplicationService;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;


/**
 * 
 * @author nmt
 *
 */
class AclController extends AbstractActionController {
	
	protected $authService;
	protected $appService;
	
	protected $userTable;
	protected $aclResourceTable;
	
	protected $doctrineEM;
	
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 * 
	 * @return \Application\Controller\ViewModel
	 */
	public function listAction() {
		$modules = $this->appService->getLoadedModules();
		
		//var_dump($modules);
		
		return new ViewModel ( array (
				'modules' => $modules,
				'e' => $this->getEvent(),
				
		) );
	}
	
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
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
				
		/* $resources = $this->aclResourceTable->getResources(0, 0);
		$totalResults = $resources->count (); */
		/* $sql="select count(*) as total_resources from nmt_application_acl_resource where 1";

		$result = $this->doctrineEM->getConnection()->query($sql)->fetchAll();
		var_dump($result); */
		
		 
    	
		$resources=$this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclResource')->findBy(array(),array('module'=>'ASC','controller'=>'ASC','action'=>'ASC'));
		$totalResults=count($resources);
		$paginator = null;
		
		if ($totalResults > $resultsPerPage) {
			$paginator = new  Paginator( $totalResults, $page, $resultsPerPage );
			$resources=$this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclResource')->findBy(array(),array('module'=>'ASC','controller'=>'ASC','action'=>'ASC'),($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
	
		return new  ViewModel( array (
				'total_resources' => $totalResults,
				'resources' => $resources,
				'paginator' => $paginator,
		) );
	}
	
	
	/**
	 * @copyright nmt@mascot.dk
	 * @return \Zend\View\Model\ViewModel
	 */
	public function updateResourcesAction() {
		
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$resources= $this->appService->getAppLoadedResources();
		
		
		$counter=0;
		foreach($resources as $res){
			$saved_res =$this->doctrineEM->getRepository( 'Application\Entity\NmtApplicationAclResource')->findBy(array('resource'=>$res['resource']));
			
			if($saved_res == null){
				$counter++;
				$input = new \Application\Entity\NmtApplicationAclResource();
				$input->setModule($res['module']);
				$input->setController($res['controller']);
				$input->setController($res['controller']);
				$input->setAction($res['action']);
				$input->setResource($res['resource']);
				$input->setCreatedOn(new \DateTime ());
				$input->setType("ROUTE");
				$input->setRemarks("created by " . $user['firstname'] . " ". $user['lastname']);
				$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user['id']);
				$input->setCreatedBy($u);
				$this->doctrineEM->persist ( $input);
				$this->doctrineEM->flush ();
			}
		
			/* if(!$this->aclResourceTable->isResourceExits($res))
			{
				$input= new AclResource();
				$input->resource = $res;
				$input->type = "ROUTE";
				$this->aclResourceTable->add($input);
			} */
		}
		return new ViewModel ( array (
					'counter'=>$counter,
	
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
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	
	
	
	

}
