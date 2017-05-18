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
use Application\Entity\NmtProcurePrRow;

/**
 *
 * @author nmt
 *        
 */
class PrRowController extends AbstractActionController {
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
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->findBy ( $criteria, $sort_criteria );
		
		if (count ( $list ) > 0) {
			foreach ( $list as $entity ) {
				$entity->setChecksum ( md5 ( uniqid ( "pr_row_" . $entity->getId () ) . microtime () ) );
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
		
		if ($request->isPost ()) {
			
			$errors = array ();
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$target_id = $request->getPost ( 'target_id' );
			$token = $request->getPost ( 'token' );
			
			$criteria = array (
					'id' => $target_id,
					'token' => $token 
			);
			
			/**
			 *
			 * @todo : Update Target
			 */
			$target = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePr' )->findOneBy ( $criteria );
			
			if ($target == null) {
				
				$errors [] = 'Target object can\'t be empty. Or token key is not valid!';
				$this->flashMessenger ()->addMessage ( 'Something wrong!' );
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'errors' => $errors,
						'target' => null,
						'entity' => null 
				) );
				
				// might need redirect
			} else {
				
				$errors = array ();
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$edt = $request->getPost ( 'edt' );
				$isActive = $request->getPost ( 'isActive' );
				$priority = $request->getPost ( 'priority' );
				$quantity = $request->getPost ( 'quantity' );
				
				$remarks = $request->getPost ( 'remarks' );
				$rowCode = $request->getPost ( 'rowCode' );
				$rowName = $request->getPost ( 'rowName' );
				$rowUnit = $request->getPost ( 'rowUnit' );
				
				$item_id= $request->getPost ( 'item_id' );
				
				
				if ($isActive != 1) {
					$isActive = 0;
				}
					
				/**
				 *
				 * @todo :
				 */
				$entity = new NmtProcurePrRow ();
				
				if ($item_id> 0) {
					$item= $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $item_id);
					$entity->setItem ($item);
				}
				
				$entity->setPr ( $target );
				$entity->setIsActive ( $isActive );
				$entity->setPriority ( $priority );
				$entity->setRemarks ( $remarks );
				$entity->setRowCode ( $rowCode );
				$entity->setRowName ( $rowName );
				$entity->setRowUnit ( $rowUnit );
				
				if ($quantity== null) {
					$errors [] = 'Please  enter order quantity!';
				} else {
					
					if (! is_numeric ( $quantity)) {
						$errors [] = '$quantity must be a number.';
					} else {
						if ($quantity<= 0) {
							$errors [] = '$quantity must be greater than 0!';
						}
						$entity->setQuantity( $quantity);
					}
				}
				
				$validator = new Date ();
				
				// Empty is OK
				if ($edt!== null) {
					if ($edt!== "") {
						
						if (! $validator->isValid ( $edt)) {
							$errors [] = 'Date is not correct or empty!';
						} else {
							$entity->setEdt(new \DateTime($edt));
						}
					}
				}
				
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							
							'redirectUrl' => $redirectUrl,
							'errors' => $errors,
							'entity' => $entity,
							'target' => $target,
							
					) );
				}
				
				// NO ERROR
				$entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
				
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
				$entity->setChecksum ( md5 ( uniqid ( "pr_row_" . $entity->getId () ) . microtime () ) );
				$this->doctrineEM->flush ();
				
				$this->flashMessenger ()->addMessage ( "Row '".  $entity->getId ."' has been created successfully!" );
				return $this->redirect ()->toUrl ( $redirectUrl );
				
			}
		}
		
		$redirectUrl = null;
		if ($this->getRequest ()->getHeader ( 'Referer' ) !== null) {
			$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		$token = $this->params ()->fromQuery ( 'token' );
		$criteria = array (
				'id' => $id,
				'checksum' => $checksum,
				'token' => $token 
		);
		
		/**
		 *
		 * @todo : Update Target
		 */
		$target = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePr' )->findOneBy ( $criteria );
		
		if ($target !== null) {
			
			return new ViewModel ( array (
					'redirectUrl' => $redirectUrl,
					'errors' => null,
					'target' => $target,
					'entity' => null 
			) );
		} else {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
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
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function list1Action() {
		$request = $this->getRequest ();
		
		// accepted only ajax request
		if (! $request->isXmlHttpRequest ()) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		;
		
		$this->layout ( "layout/user/ajax" );
		
		$target_id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		$token = $this->params ()->fromQuery ( 'token' );
		$criteria = array (
				'id' => $target_id,
				'checksum' => $checksum,
				'token' => $token 
		);
		
		/**
		 *
		 * @todo : Change Target
		 */
		$target = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePr' )->findOneBy ( $criteria );
		
		if ($target !== null) {
			
			$criteria = array (
					'pr' => $target_id 
				// 'isActive' => 1,
			);
			
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->findBy ( $criteria );
			$total_records = count ( $list );
			$paginator = null;
			
			return new ViewModel ( array (
					'list' => $list,
					'total_records' => $total_records,
					'paginator' => $paginator,
					'target' => $target 
			) );
		} else {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
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
				
				// $entity = new NmtProcurePr ();
				
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
