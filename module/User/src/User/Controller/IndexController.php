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
use Zend\Console\Request;

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
			return $this->redirect ()->toRoute ( 'Inventory' );
		}
		
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$input = new User ();
			$input->firstname = $request->getPost ( 'firstname' );
			$input->lastname = $request->getPost ( 'lastname' );
			$input->password = $request->getPost ( 'password' );
			$input->email = $request->getPost ( 'email' );
			$input->block = 0;
			$input->registration_key = Files::generate_random_string ();
			
			$errors = array ();
			
			$validator = new EmailAddress ();
			
			if ($input->firstname == '') {
				$errors [] = 'Please give your first name';
			}
			
			if ($input->lastname == '') {
				$errors [] = 'Please give your last name';
			}
			
			if (strlen ( $input->password ) < 6) {
				$errors [] = 'Password is too short. Length muss be at least 6';
			} else {
				
				if ($input->password != $request->getPost ( 'password1' )) {
					$errors [] = 'Password are not matched';
				}
			}
			
			if (! $validator->isValid ( $input->email )) {
				$errors [] = 'Email addresse is not correct!';
			} else {
				$u = $this->getUserTable ()->getUserByEmail ( $input->email );
				
				if (count ( $u ) > 0) {
					$errors [] = 'Email exists already!';
				}
			}
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'messages' => $errors 
				) );
			}
			
			$input->password = md5 ( $input->password );
			
			$newId = $this->getUserTable ()->add ( $input );
			
			$this->getRegisterService ()->doRegister ( $input );
			
			return $this->redirect ()->toRoute ( 'Inventory' );
		}
		
		return new ViewModel ( array (
				'messages' => '' 
		) );
	}
	
	
	public function consoleAction() {
		$request = $this->getRequest ();
		
		// Make sure that we are running in a console and the user has not tricked our
		// application into running this action from a public web server.
		if (! $request instanceof \Zend\Console\Request) {
			throw new \RuntimeException ( 'You can only use this action from a console-- NMT!' );
		}
		
		// Get user email from console and check if the user used --verbose or -v flag
		// $userEmail = $request->getParam('userEmail');
		// $verbose = $request->getParam('verbose') || $request->getParam('v');
		
		// reset new password
		// $newPassword = Rand::getString(16);
		
		// Fetch the user and change his password, then email him ...
		// [...]
		/*
		 * if (!$verbose) {
		 * return "Done! $userEmail has received an email with his new password.\n";
		 * }else{
		 * return "Done! New password for user $userEmail is '$newPassword'. It has also been emailed to him. \n";
		 * }
		 */
		
		$users = $this->getUserTable ()->fetchAll ();
		
		foreach ( $users as $user ) {
			
			echo $user->email;
		}
		
		return "test sucessfully";
	}
	public function confirmAction() {
	}
	/**
	 * 
	 */
	public function denyAction() {
		
		$request = $this->getRequest();
		if(!$request->isXmlHttpRequest()){
				$this->layout("layout/layout");
		}
		
		return new ViewModel ( array (
				'isAjax' => $request->isXmlHttpRequest(),
		) );
		
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
