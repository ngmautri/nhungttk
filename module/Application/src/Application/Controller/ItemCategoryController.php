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
use Application\Service\ItemCategoryService;

use Application\Entity\NmtApplicationAclRole;
use Doctrine\ORM\EntityManager;
use Application\Entity\NmtApplicationAclUserRole;
use User\Model\UserTable;
use Application\Entity\NmtApplicationAclRoleResource;
use Application\Entity\NmtInventoryItemCategory;


/**
 *
 * @author nmt
 *        
 */
class ItemCategoryController extends AbstractActionController {
	const ROOT_NODE = '_ROOT_';
	protected $SmtpTransportService;
	protected $authService;
	protected $userTable;
	protected $tree;
	protected $itemCategoryService;
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
		$identity = $this->identity();
		$u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers' )->findOneBy(array('email'=>$identity));
		
		$status = "initial...";
		
		// create ROOT NODE
		$e = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemCategory' )->findBy ( array (
				'nodeName' => self::ROOT_NODE 
		) );
		if (count ( $e ) == 0) {
			// create ROOT
			
			$input = new NmtInventoryItemCategory();
			$input->setNodeName ( self::ROOT_NODE );
			$input->setPathDepth ( 1 );
			$input->setRemarks( 'Node Root' );
			$input->setNodeCreatedBy ( $u );
			$input->setNodeCreatedOn ( new \DateTime () );
			$this->doctrineEM->persist ( $input );
			$this->doctrineEM->flush ( $input );
			$root_id = $input->getNodeId ();
			$root_node = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItemCategory', $root_id );
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
	 *  Creat
	 */
	public function addAction() {
		$request = $this->getRequest ();
		$identity = $this->identity();
		$u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers' )->findOneBy(array('email'=>$identity));
		$parent_id = ( int ) $this->params ()->fromQuery ( 'parent_id' );
		
		if ($request->isPost ()) {
			
			// $input->status = $request->getPost ( 'status' );
			// $input->remarks = $request->getPost ( 'description' );
			
			$node_name = $request->getPost ( 'node_name' );
			
			$errors = array ();
			
			if ($node_name=== '' or $node_name=== null) {
				$errors [] = 'Please give the name of department';
			}
			
			$r = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemCategory' )->findBy ( array (
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
			$parent_entity = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItemCategory', $parent_id );
			// var_dump($parent_entity->getPath());
			
			$entity = new NmtInventoryItemCategory();
			$entity->setNodeName( $node_name );
			$entity->setNodeParentId ( $parent_entity->getNodeId () );
			$entity->setNodeCreatedOn( new \DateTime() );
			$entity->setNodeCreatedby( $u );
			$entity->setStatus ( "activated" );
			$entity->setRemarks ( 'created');
			
			$this->doctrineEM->persist ( $entity );
			$this->doctrineEM->flush ();
			$new_id = $entity->getNodeId ();
			
			$new_entity = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItemCategory', $new_id );
			$new_entity->setPath ( $parent_entity->getPath () . $new_id . '/' );
			
			$a = explode ( '/', $new_entity->getPath () );
			$new_entity->setPathDepth ( count ( $a ) - 1 );
			
			$this->doctrineEM->flush ();
		}
		
		$node = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemCategory' )->findAll ();
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
		
		$root = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemCategory')->findOneBy(array("nodeName"=>"_ROOT_"));
		
		$this->itemCategoryService->initCategory();
		$this->itemCategoryService->updateCategory($root->getNodeId(),0);
		$jsTree = $this->itemCategoryService->generateJSTree1($root->getNodeId());
		
		$request = $this->getRequest ();
		
		 if ($request->isXmlHttpRequest ()) {
		 $this->layout ( "layout/user/ajax" );
		 }
		
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
		$root = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemCategory')->findOneBy(array("nodeName"=>"_ROOT_"));
		
		$this->itemCategoryService->initCategory();
		$this->itemCategoryService->updateCategory($root->getNodeId(),0);
		$jsTree = $this->itemCategoryService->generateJSTree($root->getNodeId());
		
		//$jsTree = $this->tree;
		return new ViewModel ( array (
				'jsTree' => $jsTree
		) );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function showAction() {
		$request = $this->getRequest ();
		//$user = $this->userTable->getUserByEmail ( $this->identity());
	
		$cat_id = $this->params ()->fromQuery ( 'cat_id' );
		if ($cat_id > 0) {
			$records = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemCategoryMember')->findBy(array('category'=>$cat_id));
		}
		
		$total_records= count ( $records);
		$paginator = null;
		
		if ($request->isXmlHttpRequest ()) {
			$this->layout ( "layout/inventory/ajax" );
		}
		
		return new ViewModel ( array (
				'records' => $records,
				'total_records' => $total_records,
				'paginator' => $paginator
				
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
		
		// No Doctrine
		$users = $this->aclRoleTable->getNoneMembersOfRole ( $id );
		
		return new ViewModel ( array (
				'role' => $role,
				'users' => $users,
				'redirectUrl' => $redirectUrl 
		) );
	}
	
	/**
	 *
	 * @deprecated
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addMemberActionOld() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			$role_id = ( int ) $request->getPost ( 'id' );
			$user_id_list = $request->getPost ( 'users' );
			
			if (count ( $user_id_list ) > 0) {
				
				foreach ( $user_id_list as $user_id ) {
					$member = new AclUserRole ();
					$member->role_id = $role_id;
					$member->user_id = $user_id;
					$member->updated_by = $user ['id'];
					
					if ($this->aclUserRoleTable->isMember ( $user_id, $role_id ) == false) {
						$this->aclUserRoleTable->add ( $member );
					}
				}
				
				/*
				 * return new ViewModel ( array (
				 * 'sparepart' => null,
				 * 'categories' => $categories,
				 *
				 * ) );
				 */
				
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$role = $this->aclRoleTable->getRole ( $id );
		
		$users = $this->aclUserRoleTable->getNoneMembersOfRole ( $id );
		
		return new ViewModel ( array (
				'role' => $role,
				'users' => $users,
				'redirectUrl' => $redirectUrl 
		) );
	}
	
	/**
	 *
	 * @deprecated create New Role
	 */
	public function addActionOld() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			// $input->status = $request->getPost ( 'status' );
			// $input->remarks = $request->getPost ( 'description' );
			
			$role_name = $request->getPost ( 'role' );
			
			$errors = array ();
			
			if ($role_name === '' or $role_name === null) {
				$errors [] = 'Please give role name';
			}
			
			if ($this->aclRoleTable->isRoleExits ( $role_name ) === true) {
				$errors [] = $role_name . ' exists';
			}
			
			$response = $this->getResponse ();
			
			if (count ( $errors ) > 0) {
				$c = array (
						'status' => '0',
						'messages' => $errors 
				);
				$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
				$response->setContent ( json_encode ( $c ) );
				return $response;
			}
			
			$input = new AclRole ();
			$input->role = $role_name;
			$input->parent_id = $this->aclRoleTable->getRoleIDByName ( $request->getPost ( 'parent_name' ) );
			$input->created_by = $user ["id"];
			
			// actually Role_name
			$role_id = $request->getPost ( 'role_id' );
			
			if ($this->aclRoleTable->isRoleExits ( $role_id ) === true) {
				$this->aclRoleTable->updateByRole ( $input, $role_id );
				
				$c = array (
						'status' => '1',
						'messages' => array (
								"Updated" 
						) 
				);
				$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
				$response->setContent ( json_encode ( $c ) );
				return $response;
			} else {
				// role name must unique
				$new_role_id = $this->aclRoleTable->add ( $input );
				
				// get path of parent and update new role
				if ($input->parent_id !== null) :
					$path = $this->aclRoleTable->getRole ( $input->parent_id )->path;
					$path = $path . $new_role_id . '/';
					$input->path = $path;
				else :
					$input->path = $new_role_id . '/';
				endif;
				
				$a = explode ( '/', $input->path );
				$input->path_depth = count ( $a ) - 1;
				$new_role_id = $this->aclRoleTable->update ( $input, $new_role_id );
				
				$c = array (
						'status' => '1',
						'messages' => array (
								"Created" 
						) 
				);
				$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
				$response->setContent ( json_encode ( $c ) );
				return $response;
			}
		}
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function grantAccessAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			$role_id = ( int ) $request->getPost ( 'role_id' );
			$resources = $request->getPost ( 'resources' );
			
			if (count ( $resources ) > 0) {
				
				foreach ( $resources as $r ) {
					
					// if not granted
					$criteria = array (
							'role' => $role_id,
							'resource' => $r 
					);
					
					$isGranted = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationAclRoleResource' )->findBy ( $criteria );
					
					if (count ( $isGranted ) == 0) {
						$e = new NmtApplicationAclRoleResource ();
						
						$role = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationAclRole', $role_id );
						$e->setRole ( $role );
						
						$resources = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationAclResource', $r );
						$e->setResource ( $resources );
						
						$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user ['id'] );
						$e->setUpdatedBy ( $u );
						
						$e->setUpdatedOn ( new \DateTime () );
						$this->doctrineEM->persist ( $e );
						$this->doctrineEM->flush ();
					}
					
					/*
					 * if ($this->aclRoleResourceTable->isGrantedAccess ( $role_id, $r ) == false) {
					 * $access = new AclRoleResource ();
					 * $access->resource_id = $r;
					 * $access->role_id = $role_id;
					 * $this->aclRoleResourceTable->add ( $access );
					 * }
					 */
				}
				
				/*
				 * return new ViewModel ( array (
				 * 'sparepart' => null,
				 * 'categories' => $categories,
				 *
				 * ) );
				 */
				$this->redirect ()->toUrl ( "/application/role/grant-access?id=" . $role_id );
			}
		}
		
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
		$role_id = $this->params ()->fromQuery ( 'id' );
		
		$resources = $this->aclRoleTable->getNoneResourcesOfRole ( $role_id, 0, 0 );
		
		$totalResults = count ( $resources );
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$resources = $this->aclRoleTable->getNoneResourcesOfRole ( $role_id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'total_resources' => $totalResults,
				'role_id' => $role_id,
				
				'resources' => $resources,
				'paginator' => $paginator 
		) );
	}
	
	/**
	 *
	 * @deprecated
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function grantAccessActionOld() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$role_id = ( int ) $request->getPost ( 'role_id' );
			$resources = $request->getPost ( 'resources' );
			
			if (count ( $resources ) > 0) {
				
				foreach ( $resources as $r ) {
					if ($this->aclRoleResourceTable->isGrantedAccess ( $role_id, $r ) == false) {
						$access = new AclRoleResource ();
						$access->resource_id = $r;
						$access->role_id = $role_id;
						$this->aclRoleResourceTable->add ( $access );
					}
				}
				
				/*
				 * return new ViewModel ( array (
				 * 'sparepart' => null,
				 * 'categories' => $categories,
				 *
				 * ) );
				 */
				$this->redirect ()->toUrl ( "/user/role/grant-access?id=" . $role_id );
			}
		}
		
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
		$role_id = $this->params ()->fromQuery ( 'id' );
		
		$resources = $this->aclResourceTable->getNoneResourcesOfRole ( $role_id, 0, 0 );
		$totalResults = $resources->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$resources = $this->aclResourceTable->getNoneResourcesOfRole ( $role_id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'total_resources' => $totalResults,
				'role_id' => $role_id,
				
				'resources' => $resources,
				'paginator' => $paginator 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function grantAccess1Action() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$role_id = ( int ) $request->getPost ( 'role_id' );
			$resources = $request->getPost ( 'resources' );
			
			if (count ( $resources ) > 0) {
				
				foreach ( $resources as $r ) {
					if ($this->aclRoleResourceTable->isGrantedAccess ( $role_id, $r ) == false) {
						$access = new AclRoleResource ();
						$access->resource_id = $r;
						$access->role_id = $role_id;
						$this->aclRoleResourceTable->add ( $access );
					}
				}
				
				/*
				 * return new ViewModel ( array (
				 * 'sparepart' => null,
				 * 'categories' => $categories,
				 *
				 * ) );
				 */
				$this->redirect ()->toUrl ( "/user/role/grant-access?id=" . $role_id );
			}
		}
		
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
		$role = $this->params ()->fromQuery ( 'role' );
		$role_id = $this->aclRoleTable->getRoleIDByName ( $role );
		
		$resources = $this->aclResourceTable->getNoneResourcesOfRole ( $role_id, 0, 0 );
		$totalResults = $resources->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$resources = $this->aclResourceTable->getNoneResourcesOfRole ( $role_id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'total_resources' => $totalResults,
				'role_id' => $role_id,
				
				'resources' => $resources,
				'paginator' => $paginator 
		) );
	}
	public function getAclRoleTable() {
		return $this->aclRoleTable;
	}
	public function setAclRoleTable(AclRoleTable $aclRoleTable) {
		$this->aclRoleTable = $aclRoleTable;
		return $this;
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
	public function getItemCategoryService() {
		return $this->itemCategoryService;
	}
	public function setItemCategoryService(ItemCategoryService $itemCategoryService) {
		$this->itemCategoryService = $itemCategoryService;
		return $this;
	}
	
	
}
