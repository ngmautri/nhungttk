<?php


namespace Application\Controller;

use Application\Entity\NmtApplicationAclUserRole;
use Application\Entity\NmtApplicationDepartment;
use Application\Model\AclRoleTable;
use Application\Service\DepartmentService;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentController extends AbstractActionController {
	const ROOT_NODE = '_COMPANY_';
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
	    
	    $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
	        'email' => $this->identity()
	    ));
	    
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
			$input->setRemarks( 'Node Root' );
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
	 *  Create new Department
	 */
	public function addAction() {
	    
	    $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
	        'email' => $this->identity()
	    ));
	    
		$parent_id = ( int ) $this->params ()->fromQuery ( 'parent_id' );
		
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			// $input->status = $request->getPost ( 'status' );
			// $input->remarks = $request->getPost ( 'description' );
			
			$node_name = $request->getPost ( 'node_name' );
			
			$errors = array ();
			
			if ($node_name=== '' or $node_name=== null) {
				$errors [] = 'Please give the name of department';
			}
			
			$r = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationDepartment' )->findBy ( array (
					'nodeName' => $node_name
			) );
			
			if (count($r)>=1) {
				$errors [] = $node_name. ' exists';
			}
			
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'errors' => $errors,
						'nodes' => null,
						'parent_id' => null 
				
				) );
			}
			
			// No Error
			$parent_id = $request->getPost ( 'parent_id' );
			$parent_entity = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationDepartment', $parent_id );
			// var_dump($parent_entity->getPath());
			
			$entity = new NmtApplicationDepartment();
			$entity->setNodeName( $node_name );
			$entity->setNodeParentId ( $parent_entity->getNodeId () );
			$entity->setCreatedOn( new \DateTime() );
			$entity->setCreatedBy( $u );
			$entity->setStatus ( "activated" );
			$entity->setRemarks ('done');
			
			$this->doctrineEM->persist ( $entity );
			$this->doctrineEM->flush ();
			$new_id = $entity->getNodeId ();
			
			$new_entity = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationDepartment', $new_id );
			$new_entity->setPath ( $parent_entity->getPath () . $new_id . '/' );
			
			$a = explode ( '/', $new_entity->getPath () );
			$new_entity->setPathDepth ( count ( $a ) - 1 );
			
			$this->doctrineEM->flush ();
		}
		
		$node = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationDepartment' )->findAll ();
		/*
		 * if ($request->isXmlHttpRequest ()) {
		 * $this->layout ( "layout/inventory/ajax" );
		 * }
		 */
		return new ViewModel ( array (
				'errors' => null,
				'nodes' => $node,
				'parent_id' => $parent_id 
		
		) );
	}
	
	/**
	 */
	public function listAction() {
	    
	  	$this->departmentService->initCategory();
		$this->departmentService->updateCategory(1,0);
		$jsTree = $this->departmentService->generateJSTree(1);
		
		//$jsTree = $this->tree;
		return new ViewModel ( array (
				'jsTree' => $jsTree 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function list1Action() {
	    
	    
		
		$request = $this->getRequest ();
		
		// accepted only ajax request
		if (!$request->isXmlHttpRequest ()) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		$this->layout ( "layout/user/ajax" );
		
		$this->departmentService->initCategory();
		$this->departmentService->updateCategory(1,0);
		$jsTree = $this->departmentService->generateJSTree(1);
		
		$this->getResponse()->getHeaders ()->addHeaderLine('Expires', '3800', true);
		$this->getResponse()->getHeaders ()->addHeaderLine('Cache-Control', 'public', true);
		$this->getResponse()->getHeaders ()->addHeaderLine('Cache-Control', 'max-age=3800');
		$this->getResponse()->getHeaders ()->addHeaderLine('Pragma', '', true);
		
		
		//$jsTree = $this->tree;
		return new ViewModel ( array (
				'jsTree' => $jsTree
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addMemberAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
		    'email' => $this->identity()
		));
		
		
		if ($request->isPost ()) {
			
			$role_id = ( int ) $request->getPost ( 'id' );
			$user_id_list = $request->getPost ( 'users' );
			
			if (count ( $user_id_list ) > 0) {
				foreach ( $user_id_list as $member_id ) {
					
					/*
					 * $member = new AclUserRole ();
					 * $member->role_id = $role_id;
					 * $member->user_id = $user_id;
					 * $member->updated_by = $user ['id'];
					 */
					// echo $member_id;
					
					$criteria = array (
							'user' => $member_id,
							'role' => $role_id 
					);
					
					$isMember = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationAclUserRole' )->findBy ( $criteria );
					// var_dump($isMember);
					if (count ( $isMember ) == 0) {
						$member = new NmtApplicationAclUserRole ();
						$role = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationAclRole', $role_id );
						$member->setRole ( $role );
						$m = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $member_id );
						$member->setUser ( $m );
						$member->setUpdatedBy ( $u );
						$member->setUpdatedOn ( new \DateTime () );
						$this->doctrineEM->persist ( $member );
						$this->doctrineEM->flush ();
					}
				}
				
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		// $role = $this->aclRoleTable->getRole ( $id );
		$role = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationAclRole', $id );
		
		/** @todo */
		$users = $this->aclRoleTable->getNoneMembersOfRole ( $id );
		
		return new ViewModel ( array (
				'role' => $role,
				'users' => $users,
				'redirectUrl' => $redirectUrl 
		) );
	}
	
	
	
	public function getAclRoleTable() {
		return $this->aclRoleTable;
	}
	public function setAclRoleTable(AclRoleTable $aclRoleTable) {
		$this->aclRoleTable = $aclRoleTable;
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
