<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace BP\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use Application\Entity\NmtBpVendor;
use MLA\Paginator;
use Zend\Math\Rand;
use Exception;
use BP\Service\VendorSearchService;

/*
 * Control Panel Controller
 */
class VendorController extends AbstractActionController {
	const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
	protected $doctrineEM;
	protected $vendorSearchService;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
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
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$entity_id = ( int ) $this->params ()->fromQuery ( 'entity_id' );
		$token = $this->params ()->fromQuery ( 'token' );
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		
		$criteria = array (
				'id' => $entity_id,
				'token' => $token,
				'checksum' => $checksum 
		);
		
		$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtBpVendor' )->findOneBy ( $criteria );
		if ($entity == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'entity' => $entity 
		) );
	}
	
	/*
	 * Defaul Action
	 */
	public function addAction() {
		$request = $this->getRequest ();
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
				'email' => $this->identity () 
		) );
		
		if ($request->isPost ()) {
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$errors = array ();
			
			$vendorNumber = $request->getPost ( 'vendorNumber' );
			$vendorName = $request->getPost ( 'vendorName' );
			$vendorShortName = $request->getPost ( 'vendorShortName' );
			$keywords = $request->getPost ( 'keywords' );
			$isActive = ( int ) $request->getPost ( 'isActive' );
			$remarks = $request->getPost ( 'remarks' );
			
			$country_id = $request->getPost ( 'country_id' );
			
			if ($isActive !== 1) :
				$isActive = 0;
			endif;
			
			$entity = new NmtBpVendor ();
			
			$entity->setVendorNumber($vendorNumber);
			$entity->setIsActive($isActive);
			$entity->setKeywords ( $keywords );
			$entity->setRemarks ( $remarks );
			$entity->setVendorName ( $vendorName );
			$entity->setVendorShortName ( $vendorShortName );
			
			if ($vendorName === '' or $vendorName === null) {
				$errors [] = 'Please give vendor name';
			}
			
			if ($country_id === '' or $country_id === null) {
				$errors [] = 'Please give a country!';
			} else {
				$country = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCountry', $country_id );
				$entity->setCountry ( $country );
			}
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'errors' => $errors,
						'redirectUrl' => $redirectUrl,
						'entity' => $entity 
				) );
			}
			
			// No Error
			try {
				
				$entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
				
				$entity->setCreatedOn ( new \DateTime () );
				$entity->setCreatedBy ( $u );
				
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				
				$new_entity_id = $entity->getId ();
				
				$entity->setChecksum ( md5 ( $new_entity_id . uniqid ( microtime () ) ) );
				$this->doctrineEM->flush ();
				
				$this->vendorSearchService->updateIndex(1, $entity, false);
				
			} catch ( Exception $e ) {
				return new ViewModel ( array (
						'errors' => $e->getMessage (),
						'redirectUrl' => $redirectUrl,
						'entity' => null 
				) );
			}
			
			$this->flashMessenger ()->addSuccessMessage ( 'Vendor " ' . $vendorName . '" has been created sucessfully!' );
			return $this->redirect ()->toUrl ( $redirectUrl );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		return new ViewModel ( array (
				'errors' => null,
				'redirectUrl' => $redirectUrl,
				'entity' => null 
		) );
	}
	
	/**
	 *
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function editAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			$errors = array ();
			
			$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
					"email" => $this->identity () 
			) );
			
			$errors = array ();
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$entity_id = ( int ) $request->getPost ( 'entity_id' );
			$token = $request->getPost ( 'token' );
			
			$criteria = array (
					'id' => $entity_id,
					'token' => $token 
			);
		
			$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtBpVendor' )->findOneBy ( $criteria );
			
			if ($entity == null) {
				
				$errors [] = 'Entity object can\'t be empty!';
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'errors' => $errors,
						'entity' => null 
				) );
				
				// might need redirect
			} else {
				
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				$errors = array ();
				
				$vendorNumber = $request->getPost ( 'vendorNumber' );
				
				
				$vendorName = $request->getPost ( 'vendorName' );
				$vendorShortName = $request->getPost ( 'vendorShortName' );
				$keywords = $request->getPost ( 'keywords' );
				$isActive = ( int ) $request->getPost ( 'isActive' );
				$remarks = $request->getPost ( 'remarks' );
				
				$country_id = $request->getPost ( 'country_id' );
				
				if ($isActive !== 1) :
					$isActive = 0;
				endif;
				
				//$entity = new NmtBpVendor ();
				/** @var \Application\Entity\NmtBpVendor $entity ;*/
				
				$entity->setVendorNumber($vendorNumber);
				$entity->setKeywords ( $keywords );
				$entity->setRemarks ( $remarks );
				$entity->setVendorName ( $vendorName );
				$entity->setVendorShortName ( $vendorShortName );
				$entity->setIsActive($isActive);
				
				if ($vendorName === '' or $vendorName === null) {
					$errors [] = 'Please give vendor name';
				}
				
				if ($country_id === '' or $country_id === null) {
					$errors [] = 'Please give a country!';
				} else {
					$country = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCountry', $country_id );
					$entity->setCountry ( $country );
				}
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							'errors' => $errors,
							'redirectUrl' => $redirectUrl,
							'entity' => $entity 
					) );
				}
				
				// No Error
				try {
					
					$entity->setLastChangeBy ( $u );
					$entity->setLastChangeOn ( new \DateTime () );
					$this->doctrineEM->persist ( $entity );
					$this->doctrineEM->flush ();
					
					$this->vendorSearchService->updateIndex(0, $entity, false);
					$this->flashMessenger ()->addMessage ( 'Vendor " ' . $vendorName . '" has been updated!' );
					
					return $this->redirect ()->toUrl ( $redirectUrl );
				} catch ( Exception $e ) {
					return new ViewModel ( array (
							'errors' => $e->getMessage (),
							'redirectUrl' => $redirectUrl,
							'entity' => null 
					) );
				}
			}
			
			return $this->redirect ()->toUrl ( $redirectUrl );
		}
		
		// NOT POST
		
		$redirectUrl = null;
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$entity_id = ( int ) $this->params ()->fromQuery ( 'entity_id' );
		$token = $this->params ()->fromQuery ( 'token' );
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		
		$criteria = array (
				'id' => $entity_id,
				'token' => $token,
				'checksum' => $checksum 
		);
		
		$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtBpVendor' )->findOneBy ( $criteria );
		if ($entity == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'entity' => $entity 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
		$sort_criteria = array ();
		$criteria = array ();
		
		$is_active = $this->params ()->fromQuery ( 'is_active' );
		$sort_by = $this->params ()->fromQuery ( 'sort_by' );
		$sort = $this->params ()->fromQuery ( 'sort' );
		
		
		if ($is_active == null) {
			$is_active= 1;
		}
		
		$criteria1= array ();
		
		if($is_active== 1){
			$criteria1= array (
					"isActive" => 1
			);
		}elseif($is_active == - 1){
			$criteria1= array (
					"isActive" => 0
			);
		}
		
		if ($sort_by == null) :
			$sort_by = "vendorName";
		endif;
		
		if ($sort == null) :
			$sort = "ASC";
		endif;
		
		$sort_criteria = array (
				$sort_by => $sort 
		);
		
		// $criteria = array_merge ( $criteria1, $criteria2, $criteria3);
		// var_dump($criteria);
		$criteria = $criteria1;
		
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
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtBpVendor' )->findBy ( $criteria, $sort_criteria );
		$total_records = count ( $list );
		$paginator = null;
		
		if ($total_records > $resultsPerPage) {
			$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtBpVendor' )->findBy ( $criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => $paginator,
				'sort_by' => $sort_by,
				'sort' => $sort,
				'is_active' => $is_active,
				'per_pape' => $resultsPerPage 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function list1Action() {
		$request = $this->getRequest ();
		
		$sort_criteria = array (
				"vendorName" => "ASC",
				
		);
		$criteria = array (
				"isActive" => 1,
		);
		
		$context = $this->params ()->fromQuery ( 'context' );
		
		// accepted only ajax request
		if (! $request->isXmlHttpRequest ()) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$this->layout ( "layout/user/ajax" );
		// $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtBpVendor' )->findAll ();
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtBpVendor' )->findBy ( $criteria, $sort_criteria );
		
		$total_records = count ( $list );
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => null,
				'context' => $context 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function selectAction() {
		$request = $this->getRequest ();
		
		// accepted only ajax request
		if (! $request->isXmlHttpRequest ()) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		$this->layout ( "layout/user/ajax" );
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtBpVendor' )->findAll ();
		$total_records = count ( $list );
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => null 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function updateTokenAction() {
		$criteria = array ();
		
		// var_dump($criteria);
		$sort_criteria = array ();
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtBpVendor' )->findBy ( $criteria, $sort_criteria );
		
		if (count ( $list ) > 0) {
			foreach ( $list as $entity ) {
				$entity->setChecksum ( md5 ( uniqid ( $entity->getId () ) . microtime () ) );
				$entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
			}
		}
		
		$this->doctrineEM->flush ();
		
		// update search index()
		$this->vendorSearchService->createVendorIndex();
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
	public function getVendorSearchService() {
		return $this->vendorSearchService;
	}
	public function setVendorSearchService(VendorSearchService $vendorSearchService) {
		$this->vendorSearchService = $vendorSearchService;
		return $this;
	}
	
}
