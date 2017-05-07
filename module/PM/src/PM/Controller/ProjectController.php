<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace PM\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use Application\Entity\NmtPmProject;
use Zend\Validator\Date;
use Zend\Math\Rand;

/**
 *
 * @author nmt
 *        
 */
class ProjectController extends AbstractActionController {
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
		$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
				"email" => $this->identity () 
		) );
		
		if ($request->isPost ()) {
			
			$errors = array ();
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			
			$projectName = $request->getPost ( 'projectName' );
			$keywords = $request->getPost ( 'keywords' );
			
			$description = $request->getPost ( 'description' );
			
			$startDate = $request->getPost ( 'startDate' );
			$endDate = $request->getPost ( 'endDate' );
			$isActive = $request->getPost ( 'isActive' );
			
			if ($isActive != 1) {
				$isActive = 0;
			}
			
			$status = $request->getPost ( 'status' );
			$remarks = $request->getPost ( 'remarks' );
			
			$entity = new NmtPmProject ();
			
			if ($projectName == null) {
				$errors [] = 'Please enter project name!';
			}
			
			$validator = new Date ();
			$validated_date = 0;
			
			if ($startDate !== "") {
				if (! $validator->isValid ( $startDate )) {
					$errors [] = 'Start date is not correct or empty!';
				} else {
					$entity->setStartDate ( new \DateTime ( $startDate ) );
					$validated_date ++;
				}
			}
			
			if ($endDate !== "") {
				if (! $validator->isValid ( $endDate )) {
					$errors [] = 'End date is not correct or empty!';
				} else {
					$entity->setEndDate ( new \DateTime ( $endDate ) );
					$validated_date ++;
				}
			}
			
			if ($validated_date == 2) {
				if (new \DateTime ( $endDate ) < new \DateTime ( $startDate )) {
					$errors [] = 'End date must be future!';
				}
			}
			
			$entity->setProjectName ( $projectName );
			$entity->setKeywords ( $keywords );
			$entity->setDescription ( $description );
			$entity->setIsActive ( $isActive );
			$entity->setStatus ( $status );
			$entity->setRemarks ( $remarks );
			$entity->setCreatedBy ( $u );
			$entity->setCreatedOn ( new \DateTime () );
			
			if (count ( $errors ) > 0) {
				
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'errors' => $errors,
						'entity' => $entity 
				) );
			}
			
			// NO ERROR
			$this->doctrineEM->persist ( $entity );
			$this->doctrineEM->flush ();
			// $new_entity_id = $entity->getId();
			
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
		$total_records = count ( $list );
		$paginator = null;
		
		if ($total_records > $resultsPerPage) {
			$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtPmProject' )->findBy ( $criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		// $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
		// var_dump (count($all));
		
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
		
		$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtPmProject' )->findOneBy ( $criteria );
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
		
		$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
				"email" => $this->identity () 
		) );
		
		if ($request->isPost ()) {
			
			$errors = array ();
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$entity_id = $request->getPost ( 'entity_id' );
			$entity = $this->doctrineEM->find ( 'Application\Entity\NmtPmProject', $entity_id );
			
			if ($entity == null) {
				
				$errors [] = 'Project not found';
				
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'errors' => $errors,
						'entity' => $entity 
				) );
			} else {
				
				
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$projectName = $request->getPost ( 'projectName' );
				$keywords = $request->getPost ( 'keywords' );
				
				$description = $request->getPost ( 'description' );
				
				$startDate = $request->getPost ( 'startDate' );
				$endDate = $request->getPost ( 'endDate' );
				$isActive = $request->getPost ( 'isActive' );
				
				if ($isActive == "") {
					$isActive = 0;
				}
				
				$status = $request->getPost ( 'status' );
				$remarks = $request->getPost ( 'remarks' );
				
				// $entity = new NmtPmProject ();
				
				if ($projectName == null) {
					$errors [] = 'Please enter project name!';
				}
				
				$validator = new Date ();
				$validated_date = 0;
				
				if ($startDate !== "") {
					if (! $validator->isValid ( $startDate )) {
						$errors [] = 'Start date is not correct or empty!';
					} else {
						$entity->setStartDate ( new \DateTime ( $startDate ) );
						$validated_date ++;
					}
				}
				
				if ($endDate !== "") {
					if (! $validator->isValid ( $endDate )) {
						$errors [] = 'End date is not correct or empty!';
					} else {
						$entity->setEndDate ( new \DateTime ( $endDate ) );
						$validated_date ++;
					}
				}
				
				if ($validated_date == 2) {
					if (new \DateTime ( $endDate ) < new \DateTime ( $startDate )) {
						$errors [] = 'End date must be future!';
					}
				}
				
				$entity->setProjectName ( $projectName );
				$entity->setKeywords ( $keywords );
				$entity->setDescription ( $description );
				$entity->setIsActive ( $isActive );
				$entity->setStatus ( $status );
				$entity->setRemarks ( $remarks );
				
				if (count ( $errors ) > 0) {
					
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'errors' => $errors,
							'entity' => $entity 
					) );
				}
				
				// NO ERROR
				$entity->setLastChangeBy ( $u );
				$entity->setLastChangeOn ( new \DateTime () );
				
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				// $new_entity_id = $entity->getId();
				
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
		
		$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtPmProject' )->findOneBy ( $criteria );
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
