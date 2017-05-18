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

use Zend\Math\Rand;
use Doctrine\ORM\EntityManager;

/*
 * Control Panel Controller
 */
class ProfileController extends AbstractActionController {
	
	const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
	
	protected $doctrineEM;
	protected $SmtpTransportService;
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
	 */
	public function changePasswordAction() {
			
	/* 	$request = $this->getRequest ();
		
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
		) ); */
	}

	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function updateTokenAction() {
		$criteria = array ();
		
		// var_dump($criteria);
		$sort_criteria = array ();
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findBy ( $criteria, $sort_criteria );
		
		if (count ( $list ) > 0) {
			foreach ( $list as $entity ) {
				$entity->setChecksum ( md5 ( uniqid ( "user_" . $entity->getId () ) . microtime () ) );
				$entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
			}
		}
		
		$this->doctrineEM->flush ();
		
		// update search index()
		$total_records = count ( $list );
		
		return new ViewModel ( array (
				'total_records' => $total_records
		) );
	}
	
	/**
	 *
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	
	/**
	 *
	 * @param EntityManager $doctrineEM
	 * @return \BP\Controller\VendorController
	 */
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	
}
