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
use Zend\View\Model\ViewModel;
use Application\Model\AclRoleTable;
use Nmt\Paginator;
use Application\Service\DepartmentService;

use Application\Entity\NmtApplicationAclRole;
use Doctrine\ORM\EntityManager;
use Application\Entity\NmtApplicationAclUserRole;
use User\Model\UserTable;
use Application\Entity\NmtApplicationAclRoleResource;
use Application\Entity\NmtApplicationDepartment;
use Application\Entity\NmtApplicationUom;
use Application\Entity\NmtApplicationPmtMethod;


/**
 *
 * @author nmt
 *        
 */
class PmtMethodController extends AbstractActionController {

	protected $SmtpTransportService;
	protected $authService;
	protected $userTable;
	protected $tree;
	protected $departmentService;
	protected $doctrineEM;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function initAction() {
		//$identity = $this->authService->getIdentity ();
		return new ViewModel ( array (
				'status' => $status 
		
		) );
	}
	
	/**
	 *
	 * @version 3.0
	 * @author Ngmautri
	 *        
	 *  Create new Department
	 */
	public function addAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user ['id'] );
			
		if ($request->isPost ()) {
			
			// $input->status = $request->getPost ( 'status' );
			// $input->remarks = $request->getPost ( 'description' );
			
			$pmt_method_code= $request->getPost ( 'pmt_method_code' );
			$pmt_method_name= $request->getPost ( 'pmt_method_name' );
			$description= $request->getPost ( 'description' );
			$status= $request->getPost ( 'status' );

			$errors = array ();
			
			if ($pmt_method_code=== '' or $pmt_method_code=== null) {
				$errors [] = 'Please give the name';
			}
			
			$r = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationPmtMethod' )->findBy ( array (
					'methodCode' => $pmt_method_code
			) );
			
			if (count($r)>=1) {
				$errors [] = $pmt_method_code. ' exists already';
			}
			
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'errors' => $errors,
				) );
			}
			
			// No Error
		
			$entity = new NmtApplicationPmtMethod();
			$entity->setMethodName($pmt_method_name);
			$entity->setMethodCode($pmt_method_code);
			$entity->setDescription($description);
			$entity->setStatus( $status);
			$entity->setCreatedOn( new \DateTime() );
			$entity->setCreatedBy( $u );
			
			$this->doctrineEM->persist ( $entity );
			$this->doctrineEM->flush ();
			}
		
		/*
		 * if ($request->isXmlHttpRequest ()) {
		 * $this->layout ( "layout/inventory/ajax" );
		 * }
		 */
		return new ViewModel ( array (
				'errors' => null,
		) );
	}
	
	/**
	 */
	public function listAction() {
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationPmtMethod' )->findAll();
		$total_records= count($list);
		//$jsTree = $this->tree;
		return new ViewModel ( array (
				'list' => $list,
				'total_records'=>$total_records,
				'paginator'=>null,
		) );
	}
	
	
	/**
	 */
	public function list1Action() {
		
		$request = $this->getRequest ();
		
		// accepted only ajax request
		if (!$request->isXmlHttpRequest ()) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		$this->layout ( "layout/user/ajax" );
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationPmtMethod' )->findAll();
		$total_records= count($list);
		//$jsTree = $this->tree;
		return new ViewModel ( array (
				'list' => $list,
				'total_records'=>$total_records,
				'paginator'=>null,
		) );
	}
	

	public function getSmtpTransportService() {
		return $this->SmtpTransportService;
	}
	public function setSmtpTransportService($SmtpTransportService) {
		$this->SmtpTransportService = $SmtpTransportService;
		return $this;
	}
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
	public function setUserTable(UserTable $userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	public function getDepartmentService() {
		return $this->departmentService;
	}
	public function setDepartmentService(DepartmentService $departmentService) {
		$this->departmentService = $departmentService;
		return $this;
	}
	
}
