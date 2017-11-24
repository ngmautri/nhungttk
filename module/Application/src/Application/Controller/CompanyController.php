<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Application\Entity\NmtApplicationCompany;
use Application\Entity\NmtApplicationCompanyLogo;
use Application\Entity\NmtApplicationCompanyMember;
use Application\Entity\NmtApplicationDepartment;
use Application\Listener\PictureUploadListener;
use Application\Service\DepartmentService;
use Doctrine\ORM\EntityManager;
use User\Model\UserTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author nmt
 *        
 */
class CompanyController extends AbstractActionController {
	const ROOT_NODE = '_COMPANY_';
	protected $SmtpTransportService;
	protected $authService;
	protected $userTable;
	protected $tree;
	protected $departmentService;
	protected $picUpdateListener;
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
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user ['id'] );
		
		$status = "initial...";
		
		// create ROOT NODE
		$e = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationDepartment' )->findBy ( array (
				'nodeName' => self::ROOT_NODE 
		) );
		if (count ( $e ) == 0) {
			// create super admin
			
			$input = new NmtApplicationDepartment ();
			$input->setNodeName ( self::ROOT_NODE );
			$input->setPathDepth ( 1 );
			$input->setRemarks ( 'Node Root' );
			$input->setNodeCreatedBy ( $u );
			$input->setNodeCreatedOn ( new \DateTime () );
			$this->doctrineEM->persist ( $input );
			$this->doctrineEM->flush ( $input );
			$root_id = $input->getNodeId ();
			$root_node = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationDepartment', $root_id );
			$root_node->setPath ( $root_id . '/' );
			$this->doctrineEM->flush ();
			$status = 'Root node has been created successfully: ' . $root_id;
		} else {
			$status = 'Root node has been created already.';
		}
		return new ViewModel ( array (
				'status' => $status 
		
		) );
	}
	
	/**
	 *
	 * @version 3.0
	 * @author Ngmautri
	 *        
	 * Create new Department
	 */
	public function addAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user ['id'] );
		
		if ($request->isPost ()) {
			
			// $input->status = $request->getPost ( 'status' );
			// $input->remarks = $request->getPost ( 'description' );
			
			$company_code = $request->getPost ( 'company_code' );
			$company_name = $request->getPost ( 'company_name' );
			$status = $request->getPost ( 'status' );
			
			$errors = array ();
			
			if ($company_name === '' or $company_name === null) {
				$errors [] = 'Please give the name';
			}
			
			if ($company_code === '' or $company_code === null) {
				$errors [] = 'Please give the code';
			}
			
			$r = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationCompany' )->findBy ( array (
					'companyName' => $company_name 
			) );
			
			if (count ( $r ) >= 1) {
				$errors [] = $company_name . ' exists';
			}
			
			$r = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationCompany' )->findBy ( array (
					'companyCode' => $company_code 
			) );
			
			if (count ( $r ) >= 1) {
				$errors [] = $company_code . ' exists';
			}
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'errors' => $errors 
				) );
			}
			
			// No Error
			
			$entity = new NmtApplicationCompany ();
			$entity->setCompanyCode ( $company_code );
			$entity->setCompanyName ( $company_name );
			$entity->setCreatedOn ( new \DateTime () );
			$entity->setCreatedBy ( $u );
			$entity->setStatus ( $status );
			$this->doctrineEM->persist ( $entity );
			$this->doctrineEM->flush ();
		}
		
		/*
		 * if ($request->isXmlHttpRequest ()) {
		 * $this->layout ( "layout/inventory/ajax" );
		 * }
		 */
		return new ViewModel ( array (
				'errors' => null 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationCompany' )->findAll ();
		$total_records = count ( $list );
		// $jsTree = $this->tree;
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => null 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addMemberAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user ['id'] );
		
		if ($request->isPost ()) {
			
			$company_id = ( int ) $request->getPost ( 'company_id' );
			$user_id_list = $request->getPost ( 'users' );
			
			if (count ( $user_id_list ) > 0) {
				foreach ( $user_id_list as $member_id ) {
					
					$criteria = array (
							'company' => $company_id,
							'user' => $member_id 
					);
					
					$isMember = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationCompanyMember' )->findBy ( $criteria );
					
					if (count ( $isMember ) == 0) {
						$member = new NmtApplicationCompanyMember ();
						$role = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCompany', $company_id );
						$member->setCompany ( $role );
						$m = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $member_id );
						$member->setUser ( $m );
						$member->setCreatedBy ( $u );
						$member->setCreatedOn ( new \DateTime () );
						$this->doctrineEM->persist ( $member );
						$this->doctrineEM->flush ();
					}
				}
				
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$company_id = ( int ) $this->params ()->fromQuery ( 'company_id' );
		// $role = $this->aclRoleTable->getRole ( $id );
		$company = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCompany', $company_id );
		// var_dump($company_id);
		// No Doctrine
		$users = $this->userTable->getNoneMembersOfCompany ( $company_id );
		
		return new ViewModel ( array (
				'company' => $company,
				'users' => $users,
				'redirectUrl' => $redirectUrl 
		) );
	}
	
	/**
	 */
	public function uploadLogoAction() {
		
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$user = $this->userTable->getUserByEmail ( $this->identity());
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user ['id'] );
		
		if ($request->isPost ()) {
			
			$pictures = $_POST ['pictures'];
			$id = $_POST ['target_id'];
			
			$result = "";
			
			foreach ( $pictures as $p ) {
				$filetype = $p [0];
				$result = $result . $p [2];
				$original_filename= $p [2];
				
				if (preg_match ( '/(jpg|jpeg)$/', $filetype )) {
					$ext = 'jpg';
				} else if (preg_match ( '/(gif)$/', $filetype )) {
					$ext = 'gif';
				} else if (preg_match ( '/(png)$/', $filetype )) {
					$ext = 'png';
				}
				
				$tmp_name = md5 ( $id . uniqid ( microtime () ) ) . '.' . $ext;
				
				// remove "data:image/png;base64,"
				$uri = substr ( $p [1], strpos ( $p [1], "," ) + 1 );
				
				// save to file
				file_put_contents ( $tmp_name, base64_decode ( $uri ) );
				
				$checksum = md5_file ( $tmp_name );
				
				// $root_dir = $this->articleService->getPicturesPath ();
				$root_dir = ROOT . "/data/application/picture/company/";
				
				$criteria = array (
						"checksum" => $checksum,
						"company" => $id 
				);
				
				$ck = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationCompanyLogo' )->findby ( $criteria );
				
				if (count ( $ck ) == 0) {
					$name = md5 ( $id . $checksum . uniqid ( microtime () ) ) . '.' . $ext;
					$folder = $root_dir . DIRECTORY_SEPARATOR . $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
					
					if (! is_dir ( $folder )) {
						mkdir ( $folder, 0777, true ); // important
					}
					
					rename ( $tmp_name, "$folder/$name" );
					
					$entity = new NmtApplicationCompanyLogo ();
					$entity->setUrl ( $folder . DIRECTORY_SEPARATOR . $name );
					$entity->setFiletype ( $filetype );
					$entity->setFilename ( $name );
					$entity->setOriginalFilename( $original_filename);					
					$entity->setFolder ( $folder );
					$entity->setChecksum ( $checksum );
					$company = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCompany', $id );
					$entity->setCompany ( $company );
					$entity->setCreatedBy ( $u );
					$entity->setCreatedOn ( new \DateTime () );
					
					$this->doctrineEM->persist ( $entity );
					$this->doctrineEM->flush ();
					
					// trigger uploadPicture. AbtractController is EventManagerAware.
					$this->getEventManager ()->trigger ( 'uploadPicture', __CLASS__, array (
							'picture_name' => $name,
							'pictures_dir' => $folder 
					) );
					
					$result = $result . ' uploaded. //';
				} else {
					$result = $result . ' exits. //';
				}
			}
			// $data['filetype'] = $filetype;
			$data = array ();
			$data ['message'] = $result;
			$response = $this->getResponse ();
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
			$response->setContent ( json_encode ( $data ) );
			return $response;
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		// $company = $this->articleTable->get ( $id );
		// $company = $this->doctrineEM->find('Application\Entity\NmtApplicationCompanyLogo',$id);
		$company = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCompany', $id );
		
		return new ViewModel ( array (
				'company' => $company,
				'redirectUrl' => $redirectUrl,
				'errors' => null 
		) );
	}
	
	/**
	 *
	 * @return unknown
	 */
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
	public function getPicUpdateListener() {
		return $this->picUpdateListener;
	}
	public function setPicUpdateListener(PictureUploadListener $picUpdateListener) {
		$this->picUpdateListener = $picUpdateListener;
		return $this;
	}
}
