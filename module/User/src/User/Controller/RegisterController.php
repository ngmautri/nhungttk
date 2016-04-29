<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace User\Controller;

use User\Form\UserForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Model\User;
use MLA\Files;
use Zend\Validator\EmailAddress;

class IndexController extends AbstractActionController {
	public $userTable;
	public $authService;
	public $registerService;
	public $massage = 'NULL';
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		
		// $this->NMTPlugin()->test();
		$form = new UserForm ();
		
		if ($this->getAuthService ()->hasIdentity ()) {
			$massage = "loged in";
		} else {
			return $this->redirect ()->toRoute ( 'login' );
		}
		
		return new ViewModel ( array (
				'users' => $this->getUserTable ()->fetchAll (),
				'massage' => $massage,
				'form' => $form 
		) );
	}
	public function registerAction() {
		
		// User is authenticated
		if ($this->getAuthService ()->hasIdentity ()) {
			return $this->redirect ()->toRoute ( 'assetcategory' );
		}
		
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$input = new User ();
			$input->firstname = $request->getPost ( 'firstname' );
			$input->lastname = $request->getPost ( 'lastname' );
			
			$input->password = md5 ( $request->getPost ( 'password' ) );
			$input->email = $request->getPost ( 'email' );
			$input->block = 0;			
			$input->registration_key = Files::generate_random_string ();
			
			

			$errors = array ();
			
			$validator = new EmailAddress();
			
			if ($input->firstname ==''){
				$errors [] = 'Please give your first name';
			}
			
			if ($input->lastname ==''){
				$errors [] = 'Please give your last name';
			}
			
			if (strlen($input->password)<6) {
				$errors [] = 'Password is too short. It muss be at least 6';
			}
			
			if (! $validator->isValid ( $input->email )) {
				$errors [] = 'Email addresse is not correct!';
			}else{
				$r = $this->getUserTable->getUserTable->getUserByEmail($input->email);
				
				if ($r->count()>0){
					$errors [] = 'Email addresse exist already!';
				}
			}
				
			
			if (count($errors) > 0) {
				return new ViewModel ( array (
						'messages' => $errors
				));
			}
			
			
			$newId = $this->getUserTable ()->add ( $input );
			$this->getRegisterService ()->doRegister ( $input );
			return $this->redirect ()->toRoute ( 'Inventory' );
		}
		
		return new ViewModel ( array () );
	}
	
	public function registerConfirmAction() {
	}
	
	// get UserTable
	public function getUserTable() {
		if (! $this->userTable) {
			$sm = $this->getServiceLocator ();
			$this->userTable = $sm->get ( 'User\Model\UserTable' );
		}
		return $this->userTable;
	}
	
	/**
	 * Get Authentication Service
	 */
	public function getAuthService() {
		if (! $this->authService) {
			$this->authservice = $this->getServiceLocator ()->get ( 'AuthService' );
		}
		return $this->authservice;
	}
	
	/*
	 * Get Authentication Service
	 */
	public function getRegisterService() {
		if (! $this->registerService) {
			$this->registerService = $this->getServiceLocator ()->get ( 'User\Service\RegisterService' );
		}
		return $this->registerService;
	}
}
