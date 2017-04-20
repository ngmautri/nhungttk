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
use MLA\Paginator;
use Application\Service\AclService;
use Application\Entity\NmtApplicationAclRole;
use Doctrine\ORM\EntityManager;
use Application\Entity\NmtApplicationAclUserRole;
use User\Model\UserTable;
use Application\Entity\NmtApplicationAclRoleResource;

/**
 * 
 * @author nmt
 *
 */
class RoleController extends AbstractActionController {
	const SUPER_ADMIN = 'super-administrator';
	const ADMIN = 'administrator';
	const MEMBER = 'member';
	
	protected $SmtpTransportService;
	protected $authService;
	protected $aclService;
	protected $userTable;
	protected $aclRoleTable;
	protected $tree;
	
	
	
	protected $doctrineEM;
	
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 * 
	 */
	public function initAction() {
		
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user['id']);
		
		
		// create super-administrator		
		$e = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRole')->findBy(array('role'=>'super-administrator'));
		if(count($e)==0){
			// create super admin
			
			$input = new NmtApplicationAclRole();
			$input->setRole(self::SUPER_ADMIN);
			$input->setStatus("activated");
			$input->setPathDepth(1);
			$input->setRemarks("default role");
			$input->setCreatedBy($u);
			$input->setCreatedOn(new \DateTime ());			
			$this->doctrineEM->persist ( $input);
			$this->doctrineEM->flush( $input);
			$supper_admin_id = $input->getId();
			$super_admin_role=$this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $supper_admin_id);
			$super_admin_role->setPath($supper_admin_id.'/');
			$this->doctrineEM->flush();
			
			// create admin
			$input = new NmtApplicationAclRole();
			$input->setRole(self::ADMIN);
			$input->setStatus("activated");
			$input->setParentId($supper_admin_id);			
			$input->setPathDepth(2);
			$input->setRemarks("default role");
			$input->setCreatedBy($u);
			$input->setCreatedOn(new \DateTime ());
			$this->doctrineEM->persist ( $input);
			$this->doctrineEM->flush( $input);
			$new_id = $input->getId();
			echo $new_id;
			
			$new_role=$this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $new_id);
			$new_role->setPath($super_admin_role->getPath().$new_id.'/');
			$this->doctrineEM->flush();
		}
		
		// create member
		$e = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRole')->findBy(array('role'=>self::MEMBER));
		if(count($e)==0){
			
			$input = new NmtApplicationAclRole();
			$input->setRole(self::MEMBER);
			$input->setStatus("activated");
			$input->setPathDepth(1);
			$input->setRemarks("default role");
			$input->setCreatedBy($u);
			$input->setCreatedOn(new \DateTime ());
			$this->doctrineEM->persist ( $input);
			$this->doctrineEM->flush( $input);
			$member_id = $input->getId();
			$member_role=$this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $member_id);
			$member_role->setPath($member_id.'/');
			$this->doctrineEM->flush();
		}
	}
	
	/**
	 * @version 3.0
	 * @author Ngmautri
	 * 
	 * create New Role
	 */
	public function addAction() {
		
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user['id']);
		$parent_id = ( int ) $this->params ()->fromQuery ( 'parent_id' );
		
		
		if ($request->isPost ()) {
			
			// $input->status = $request->getPost ( 'status' );
			// $input->remarks = $request->getPost ( 'description' );
			
			$role_name = $request->getPost ( 'role' );
			
			$errors = array ();
			
			if ($role_name === '' or $role_name === null) {
				$errors [] = 'Please give role name';
			}
			
			$r = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRole')->findBy(array('role'=>$role_name));
			/* if (count($r)>=1) {
				$errors [] = $role_name . ' exists';
			} */
				
			$response = $this->getResponse ();
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'errors' => $errors,
						'roles' => null,
						'parent_id' => null,
						
				) );
			}
			// Now Error
			$parent_id = $request->getPost ( 'parent_id');
			$parent_entity = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $parent_id);
			//var_dump($parent_entity->getPath());
			
			$entity= new NmtApplicationAclRole();
			$entity->setRole($role_name);			
			$entity->setParentId($parent_entity->getId());
			$entity->setCreatedOn(new \Datetime());
			$entity->setCreatedBy($u);
			$entity->setStatus("activated");
			$entity->setRemarks('Role created by '. $user['firstname'] . ' '. $user['lastname']);
			
			
			$this->doctrineEM->persist($entity);
			$this->doctrineEM->flush();
			$new_id = $entity->getId();
	//		var_dump($new_id);
			
			$new_entity=$this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $new_id);
			$new_entity->setPath($parent_entity->getPath().$new_id.'/');
			
			$a = explode ( '/', $new_entity->getPath() );
			$new_entity->setPathDepth(count ( $a ) - 1);
			
			$this->doctrineEM->flush();
		}
		
		$roles = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRole')->findAll();
/* 		if ($request->isXmlHttpRequest ()) {
			$this->layout ( "layout/inventory/ajax" );
		}	
 */		return new ViewModel ( array (
					'errors' => null,
					'roles' => $roles,
 					'parent_id' => $parent_id,
 					
			) );
	}
	
	/**
	 */
	public function listAction() {
		$data = $this->aclService->returnAclTree1 ();
		
		$this->tree ( $data, 'Super-administrator' );
		$jsTree = $this->tree;
		return new ViewModel ( array (
				'jsTree' => $jsTree 
		) );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function list1Action() {
		$roles = $this->aclService->returnAclTree ();
		
		return new ViewModel ( array (
				'roles' => $roles 
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
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user['id']);
		
		
		if ($request->isPost ()) {
			
			$role_id = ( int ) $request->getPost ( 'id' );
			$user_id_list = $request->getPost ( 'users' );
			
			if (count ( $user_id_list ) > 0) {				
				foreach ( $user_id_list as $member_id ) {
					
					/* $member = new AclUserRole ();
					$member->role_id = $role_id;
					$member->user_id = $user_id;
					$member->updated_by = $user ['id']; */
					//echo $member_id;
					
					$criteria = array('user'=>$member_id,
									'role'=>$role_id);
					
					$isMember = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclUserRole')->findBy($criteria);
					//var_dump($isMember);
					if(count($isMember) == 0){
						$member = new  NmtApplicationAclUserRole();
						$role = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $role_id);
						$member->setRole($role);
						$m = $this->doctrineEM->find('Application\Entity\MlaUsers', $member_id);
						$member->setUser($m);
						$member->setUpdatedBy($u);
						$member->setUpdatedOn(new \DateTime());
						$this->doctrineEM->persist($member);
						$this->doctrineEM->flush();					}
					
				}
				
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		//$role = $this->aclRoleTable->getRole ( $id );
		$role = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $id);		
		
		//No Doctrine
		$users = $this->aclRoleTable->getNoneMembersOfRole ( $id );
		
		return new ViewModel ( array (
				'role' => $role,
				'users' => $users,
				'redirectUrl' => $redirectUrl 
		) );
	}
	
	/**
	 * @deprecated
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
	 * @deprecated
	 * create New Role
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
					$criteria = array('role'=>$role_id,
							'resource'=>$r);
					
					$isGranted = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRoleResource')->findBy($criteria);
					
					if(count($isGranted)==0){
						$e =  new NmtApplicationAclRoleResource();
						
						$role = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $role_id);
						$e->setRole($role);
						
						$resources = $this->doctrineEM->find('Application\Entity\NmtApplicationAclResource', $r);
						$e->setResource($resources);
						
						$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user['id']);
						$e->setUpdatedBy($u);
						
						$e->setUpdatedOn(new \DateTime());
						$this->doctrineEM->persist($e);
						$this->doctrineEM->flush();
					}
					
					/* if ($this->aclRoleResourceTable->isGrantedAccess ( $role_id, $r ) == false) {
						$access = new AclRoleResource ();
						$access->resource_id = $r;
						$access->role_id = $role_id;
						$this->aclRoleResourceTable->add ( $access );
					} */
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
		
		$totalResults = count($resources);
		
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
	 *@deprecated
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
	
	public function getAclService() {
		return $this->aclService;
	}
	public function setAclService(AclService $aclService) {
		$this->aclService = $aclService;
		return $this;
	}
	
	// JS TREE
	protected function tree($data, $root) {
		$tree = $data->categories [$root];
		$children = $tree ['children'];
		
		// inorder travesal
		
		if (count ( $children ) > 0) {
			$this->tree = $this->tree . '<li id="' . $root . '" data-jstree=\'{ "opened" : true}\'> ' . ucwords ( $root ) . '(' . count ( $children ) . ")\n";
			$this->tree = $this->tree . '<ul>' . "\n";
			foreach ( $children as $c ) {
				if (count ( $c ['children'] ) > 0) {
					$this->tree ( $data, $c ['instance'] );
				} else {
					$this->tree = $this->tree . '<li id="' . $c ['instance'] . '" data-jstree=\'{}\'>' . $c ['instance'] . ' </li>' . "\n";
					$this->tree ( $data, $c ['instance'] );
				}
			}
			$this->tree = $this->tree . '</ul>' . "\n";
			
			$this->tree = $this->tree . '</li>' . "\n";
		}
	}
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	
}
