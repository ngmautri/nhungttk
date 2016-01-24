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
			$massage = "not loged in yet";
		}
		
		return new ViewModel ( array (
				'users' => $this->getUserTable ()->fetchAll (),
				'massage' => $massage,
				'form' => $form 
		) );
		
	}
	
	
	public function registerAction(){
		
		$request = $this->getRequest ();
		if ($request->isPost ()) {
				
			$request = $this->getRequest ();
			
			if ($request->isPost ()) {
		
				$input = new User();
				$input->firstname = $request->getPost ( 'firstname' );
				$input->lastname = $request->getPost ( 'lastname' );
		
				$input->password = md5($request->getPost ( 'password' ));
				$input->email = $request->getPost ( 'email' );
				$input->block = 0;
		
				$f = new Files;
				$input->registration_key = Files::generate_random_string();
								
				$newId = $this->getUserTable()->add( $input );
				
				$this->getRegisterService()->doRegister ($input);
				
				return $this->redirect ()->toRoute ( 'assetcategory' );
			}
		
		}
		return new ViewModel ( array (
				
		) );
	}
	
	public function registerConfirmAction(){
		
		
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
