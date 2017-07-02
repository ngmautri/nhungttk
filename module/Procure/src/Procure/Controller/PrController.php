<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Procure\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use Application\Entity\NmtPmProject;
use Zend\Validator\Date;
use Zend\Math\Rand;
use Application\Entity\NmtProcurePr;

/**
 *
 * @author nmt
 *        
 */
class PrController extends AbstractActionController {
	const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
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
	public function updateTokenAction() {
		$criteria = array ();
		
		// var_dump($criteria);
		
		$sort_criteria = array ();
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 15;
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
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtPmProject' )->findBy ( $criteria, $sort_criteria );
		
		if (count ( $list ) > 0) {
			foreach ( $list as $entity ) {
				$entity->setChecksum ( md5 ( uniqid ( "project_" . $entity->getId () ) . microtime () ) );
				$entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
			}
		}
		
		$this->doctrineEM->flush ();
		
		/**
		 *
		 * @todo : update index
		 */
		// $this->employeeSearchService->createEmployeeIndex();
		
		$total_records = count ( $list );
		$paginator = null;
		
		if ($total_records > $resultsPerPage) {
			$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtPmProject' )->findBy ( $criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => $paginator 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addAction() {
		$request = $this->getRequest ();
		
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		if ($request->isPost ()) {
			
			$errors = array ();
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			
			$prNumber = $request->getPost ( 'prNumber' );
			$prName = $request->getPost ( 'prName' );
			$keywords = $request->getPost ( 'keywords' );
			$submittedOn = $request->getPost ( 'submittedOn' );
			
			$remarks = $request->getPost ( 'remarks' );
			$isDraft = $request->getPost ( 'isDraft' );
			$isActive = $request->getPost ( 'isActive' );
			$status = $request->getPost ( 'status' );
			$remarks = $request->getPost ( 'remarks' );
			$department_id = $request->getPost ( 'department_id' );
			
			if ($isActive != 1) {
				$isActive = 0;
			}
			
			if ($isDraft != 1) {
				$isDraft = 0;
			}
			
			$prAutoNumber = 'later';
			
			if ($prNumber == null) {
				$errors [] = 'Please enter PR Number!';
			}
			
			if ($prName == null) {
				$errors [] = 'Please enter PR Name!';
			}
			
			$entity = new NmtProcurePr ();
			$entity->setPrAutoNumber ( $prAutoNumber );
			$entity->setPrNumber ( $prNumber );
			$entity->setPrName ( $prName );
			$entity->setKeywords ( $keywords );
			$entity->setRemarks ( $remarks );
			$entity->setIsActive ( $isActive );
			$entity->setIsDraft ( $isDraft );
			$entity->setStatus ( $status );
			
			$validator = new Date ();
			
			// Empty is OK
			if ($submittedOn !== null) {
				if ($submittedOn !== "") {
					
					if (! $validator->isValid ( $submittedOn )) {
						$errors [] = 'Date is not correct or empty!';
					} else {
						$entity->setSubmittedOn ( new \DateTime ( $submittedOn ) );
					}
				}
			}
			
			if ($department_id > 0) {
				$department = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationDepartment', $department_id );
				$entity->setDepartment ( $department );
			}
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						
						'redirectUrl' => $redirectUrl,
						'errors' => $errors,
						'entity' => $entity 
				) );
			}
			
			// NO ERROR
			$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
					"email" => $this->identity () 
			) );
			
			$entity->setCreatedBy ( $u );
			$entity->setCreatedOn ( new \DateTime () );
			
			$this->doctrineEM->persist ( $entity );
			$this->doctrineEM->flush ();
			
			/**
			 *
			 * @todo : UPDATE
			 */
			$entity->setChecksum ( md5 ( uniqid ( "pr_" . $entity->getId () ) . microtime () ) );
			$entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
			$this->doctrineEM->flush ();
			
			$this->flashMessenger ()->addMessage ( 'Purchase Request "' . $prNumber . '" is created successfully!' );
			
			$redirectUrl="/procure/pr/show?token=".$entity->getToken(). "&entity_id=".$entity->getId()."&checksum=".$entity->getChecksum();
			return $this->redirect ()->toUrl ( $redirectUrl );
		}
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'entity' => null 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
		$this->layout ( "layout/fluid" );
		
		$criteria = array ();
		
		// var_dump($criteria);
		
		$sort_criteria = array ();
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 15;
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
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePr' )->findBy ( $criteria, $sort_criteria );
		$total_records = count ( $list );
		$paginator = null;
		
		if ($total_records > $resultsPerPage) {
			$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePr' )->findBy ( $criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => $paginator 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function allAction() {
		//$this->layout ( "layout/fluid" );
	    $plugin = $this->ProcureWfPlugin();
	    echo($plugin->getWF());
		
		$sort_by = $this->params ()->fromQuery ( 'sort_by' );
		$sort = $this->params ()->fromQuery ( 'sort' );
		$balance = $this->params ()->fromQuery ( 'balance' );
		$is_active = (int) $this->params ()->fromQuery ( 'is_active' );
		
		if ($sort_by == null) :
			$sort_by = "prNumber";
		endif;
		
		if ($is_active == null) :
			$is_active = 1;
		endif;
		
		if ($balance == null) :
			$balance = 1;
		endif;
		
		if ($sort == null) :
			$sort = "ASC";
		endif;
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 15;
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
		
		// $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePr' )->findBy ( $criteria, $sort_criteria );
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getPrList ( $is_active,$balance, $sort_by, $sort, 0, 0 );
		
		$total_records = count ( $list );
		$paginator = null;
		
		if ($total_records > $resultsPerPage) {
			$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getPrList ( $is_active,$balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => $paginator,
				'sort_by' => $sort_by,
				'sort' => $sort,
				'per_pape' => $resultsPerPage,
				'balance' => $balance,
				'is_active' => $is_active 
		) );
	}
	
	/**
	 *
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function showAction() {
		$request = $this->getRequest ();
		
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		// $u = $this->doctrineEM->getRepository( 'Application\Entity\MlaUsers')->findOneBy(array("email"=>$this->identity() ));
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'entity_id' );
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		$token = $this->params ()->fromQuery ( 'token' );
		$criteria = array (
				'id' => $id,
				'checksum' => $checksum,
				'token' => $token 
		);
		
		$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePr' )->findOneBy ( $criteria );
		if ($entity !== null) {
			
			return new ViewModel ( array (
					'redirectUrl' => $redirectUrl,
					'entity' => $entity,
					'errors' => null 
			) );
		} else {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
	}
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$errors = array ();
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$entity_id = $request->getPost ( 'entity_id' );
			$token = $request->getPost ( 'token' );
			
			$criteria = array (
					'id' => $entity_id,
					'token' => $token 
			);
			
			$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePr' )->findOneBy ( $criteria );
			
			if ($entity == null) {
				
				$errors [] = 'Project not found';
				$this->flashMessenger ()->addMessage ( 'Something went wrong!' );
				
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'errors' => $errors,
						'entity' => $entity 
				) );
			} else {
				
				$prNumber = $request->getPost ( 'prNumber' );
				$prName = $request->getPost ( 'prName' );
				$keywords = $request->getPost ( 'keywords' );
				$remarks = $request->getPost ( 'remarks' );
				$isDraft = $request->getPost ( 'isDraft' );
				$isActive = $request->getPost ( 'isActive' );
				$status = $request->getPost ( 'status' );
				$remarks = $request->getPost ( 'remarks' );
				$department_id = $request->getPost ( 'department_id' );
				
				if ($isActive != 1) {
					$isActive = 0;
				}
				
				if ($isDraft != 1) {
					$isDraft = 0;
				}
				
				$prAutoNumber = 'later';
				
				if ($prNumber == null) {
					$errors [] = 'Please enter PR Number!';
				}
				
				if ($prName == null) {
					$errors [] = 'Please enter PR Name!';
				}
				
				//$entity = new NmtProcurePr ();
				
				$entity->setPrAutoNumber ( $prAutoNumber );
				$entity->setPrNumber ( $prNumber );
				$entity->setPrName ( $prName );
				$entity->setKeywords ( $keywords );
				$entity->setRemarks ( $remarks );
				$entity->setIsActive ( $isActive );
				$entity->setIsDraft ( $isDraft );
				$entity->setStatus ( $status );
				
				if ($department_id > 0) {
					$department = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationDepartment', $department_id );
					$entity->setDepartment ( $department );
				}
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							
							'redirectUrl' => $redirectUrl,
							'errors' => $errors,
							'entity' => $entity
					) );
				}
				
				// NO ERROR
				$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
						"email" => $this->identity ()
				) );
				
				$entity->setLastChangeBy($u);
				$entity->setLastChangeOn(new \DateTime());
				
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				
				$this->flashMessenger ()->addMessage ( 'Purchase Request "'. $prName .'" has been updated!' );
				return $this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'entity_id' );
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		$token = $this->params ()->fromQuery ( 'token' );
		$criteria = array (
				'id' => $id,
				'checksum' => $checksum,
				'token' => $token
		);
		
		$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePr' )->findOneBy ( $criteria );
		if ($entity !== null) {
			
			return new ViewModel ( array (
					'redirectUrl' => $redirectUrl,
					'entity' => $entity,
					'errors' => null
			) );
		} else {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
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
	 * @return \PM\Controller\IndexController
	 */
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
}
