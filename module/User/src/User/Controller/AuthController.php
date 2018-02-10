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
use Zend\Session\Container;

use User\Model\UserTable;

class AuthController extends AbstractActionController {
	
	public $userTable;
	public $authService;
	public $registerService;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/*
	 * 
	 */
	public function authenticateAction() {
	    
	    $redirect = $this->params()->fromQuery('redirect');
	
	    if($redirect==null){
	        $redirect = '/inventory/item/list';
	    }
		$this->layout("layout/layout");
		
		// User is authenticated
		if ($this->getAuthService ()->hasIdentity ()) {
			//return $this->redirect ()->toRoute ( 'Inventory' );
		    return $this->redirect ()->toUrl ( $redirect );
			
		}
		
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$email = $request->getPost ( 'email' );
			$password = $request->getPost ( 'password' );
			$redirect = $request->getPost ( 'redirect' );
			
			$errors = array ();
			
			$validator = new EmailAddress ();
			if (! $validator->isValid ( $email )) {
				$errors [] = 'Email addresse is not correct!';
			}
			
			if (count ( $errors ) > 0) {
			    
			    // trigger uploadPicture. AbtractController is EventManagerAware.
			    $this->getEventManager()->trigger('authenticate.log', __CLASS__, array(
			        'priority' => 7,
			        'message' => 'Access not successful!'
			    ));
			    
				return new ViewModel ( array (
						'messages' => $errors,
				        'redirect' => $redirect,
				) );
			}
			
			$this->authService->getAdapter ()->setIdentity ( $email )->setCredential ( $password );
			$result = $this->authService->authenticate ();
			
			if ($result->isValid ()) {
				
				$user = $this->userTable->getUserByEmail($email);
				
				// create new session 
				$session = new Container('MLA_USER');
				$session->offsetSet('user', $user);
				
				// trigger uploadPicture. AbtractController is EventManagerAware.
				$this->getEventManager()->trigger('authenticate.log', __CLASS__, array(
				    'priority' => 7,
				    'message' => 'Access successful!'
				));
				
				return $this->redirect ()->toUrl ($redirect);
				
			} else {
				
				// $route = $this->getServiceLocator ()->get ( 'application' )->getMvcEvent()->getRouteMatch();
					return new ViewModel ( array (
						'messages' => $result->getMessages (),
					    'redirect' => $redirect,
					) );
			}
		}
		
		return new ViewModel ( array (
				'messages' => '',
		        'redirect' => $redirect,
		) );
	}
	public function logoutAction() {
		
		$session = new Container('MLA_USER');
		$session->getManager()->destroy();
		$this->authService->clearIdentity ();
		$this->flashmessenger ()->addMessage ( "You've been logged out" );
		
		// trigger uploadPicture. AbtractController is EventManagerAware.
		$this->getEventManager()->trigger('authenticate.log', __CLASS__, array(
		    'priority' => 7,
		    'message' => 'User logged out!'
		));
		
		return $this->redirect ()->toRoute ( 'login' );
		
		
	}
	
	// GETTER AND SETTER
	
	
	public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable(UserTable $userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getAuthService() {
		return $this->authService;
	}
	public function setAuthService($authService) {
		$this->authService = $authService;
		return $this;
	}
	public function getRegisterService() {
		return $this->registerService;
	}
	public function setRegisterService($registerService) {
		$this->registerService = $registerService;
		return $this;
	}
	
}
