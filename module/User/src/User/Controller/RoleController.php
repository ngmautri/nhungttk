<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace User\Controller;

//use Zend\I18n\Validator\Int;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use MLA\Files;
use User\Service\Acl;
use User\Model\AclRole;
use User\Model\AclRoleTable;
use User\Model\AclUserRole;
use User\Model\AclUserRoleTable;
use User\Model\AclRoleResource;
use User\Model\AclRoleResourceTable;
use User\Model\AclResource;
use User\Model\AclResourceTable;

/*
 * Control Panel Controller
 */
class RoleController extends AbstractActionController {
	protected $SmtpTransportService;
	protected $authService;
	protected $aclService;
	protected $userTable;
	protected $aclRoleTable;
	protected $aclResourceTable;
	protected $aclUserRoleTable;
	protected $aclRoleResourceTable;
	protected $tree;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 */
	public function listAction() {
		$data = $this->aclService->returnAclTree1 ();
		
		$this->tree ( $data, 'Administrator' );
		$jsTree = $this->tree;
		return new ViewModel ( array (
				'jsTree' => $jsTree 
		) );
	}
	
	/**
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
	 * create New Role
	 */
	public function addAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			 $input->status = $request->getPost ( 'status' );
			 $input->remarks = $request->getPost ( 'description' );
			
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
	public function getAclUserRoleTable() {
		return $this->aclUserRoleTable;
	}
	public function setAclUserRoleTable(AclUserRoleTable $aclUserRoleTable) {
		$this->aclUserRoleTable = $aclUserRoleTable;
		return $this;
	}
	public function getAclRoleResourceTable() {
		return $this->aclRoleResourceTable;
	}
	public function setAclRoleResourceTable(AclRoleResourceTable $aclRoleResourceTable) {
		$this->aclRoleResourceTable = $aclRoleResourceTable;
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
	public function setUserTable($userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getAclResourceTable() {
		return $this->aclResourceTable;
	}
	public function setAclResourceTable(AclResourceTable $aclResourceTable) {
		$this->aclResourceTable = $aclResourceTable;
		return $this;
	}
	public function getAclService() {
		return $this->aclService;
	}
	public function setAclService(Acl $aclService) {
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
}
