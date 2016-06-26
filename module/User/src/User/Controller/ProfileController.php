<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace User\Controller;

use Zend\I18n\Validator\Int;
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
use User\Model\UserTable;

/*
 * Control Panel Controller
 */
class ProfileController extends AbstractActionController {
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
		
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserDepartmentByEmail( $identity );
		
		return new ViewModel ( array (
				'user' => $user
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
	 */
	public function changePasswordAction() {
			
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$old_password = $request->getPost ( 'old_password' );
			$new_password = $request->getPost ( 'new_password' );
			$new_password1 = $request->getPost ( 'new_password1' );
			
			
			$identity = $this->authService->getIdentity ();
			$user = $this->userTable->getUserByEmail ( $identity );
			
			if($user['password']!= md5($old_password))
			{
				
			}
			
			$errors = array ();
			
			if($user['password']!== md5($old_password))
			{
				$errors [] = 'Old Password is wrong!';
			}
			
			// check old password
			
			if (strlen ( $new_password ) < 6) {
				$errors [] = 'Password is too short. Length muss be at least 6';
			} else {
				
				if ($new_password !== $new_password1) {
					$errors [] = 'New password are not matched';
				}
			}
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'status' =>0,
						'messages' => $errors 
				) );
			}
			
			// do change
			$this->userTable->changePassword($user['id'],md5($new_password));
			
			return new ViewModel ( array (
					'status' =>1,
					'messages' => array('Password changed successfully!')
			) );
		}
		
		return new ViewModel ( array (
				'status' =>null,
				'messages' => null
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
	public function setUserTable(UserTable $userTable) {
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
	
	
}
