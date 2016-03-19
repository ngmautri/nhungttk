<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Model\User;
use MLA\Files;
use Zend\Validator\EmailAddress;

class AuthController extends AbstractActionController {
	public $userTable;
	public $authService;
	public $registerService;
	public $massage = 'NULL';
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	public function authenticateAction() {
		
		// User is authenticated
		if ($this->getAuthService ()->hasIdentity ()) {
			return $this->redirect ()->toRoute ( 'assetcategory' );
		}
		
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$email = $request->getPost ( 'email' );
			$password = $request->getPost ( 'password' );
			
			$errors = array ();
			
			$validator = new EmailAddress ();
			if (! $validator->isValid ( $email )) {
				$errors [] = 'Email addresse is not correct!';
			}
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'messages' => $errors 
				) );
			}
			
			$this->getAuthService ()->getAdapter ()->setIdentity ( $email )->setCredential ( $password );
			
			$result = $this->getAuthService ()->authenticate ();
			
			if ($result->isValid ()) {
				return $this->redirect ()->toRoute ( 'Inventory' );
			} else {
				
				// $route = $this->getServiceLocator ()->get ( 'application' )->getMvcEvent()->getRouteMatch();
					return new ViewModel ( array (
						'messages' => $result->getMessages () 
				) );
			}
		}
		
		return new ViewModel ( array (
				'messages' => '' 
		) );
	}
	public function logoutAction() {
		$this->getAuthService ()->clearIdentity ();
		$this->flashmessenger ()->addMessage ( "You've been logged out" );
		return $this->redirect ()->toRoute ( 'login' );
	}
	
	// get UserTable
	public function getUserTable() {
		if (! $this->userTable) {
			$sm = $this->getServiceLocator ();
			$this->userTable = $sm->get ( 'User\Model\UserTable' );
		}
		return $this->userTable;
	}
	
	/*
	 * Get Authentication Service
	 */
	public function getAuthService() {
		if (! $this->authService) {
			$this->authservice = $this->getServiceLocator ()->get ( 'AuthService' );
		}
		return $this->authservice;
	}
}
