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

/*
 * Control Panel Controller
 */
class VendorController extends AbstractActionController {
	protected $doctrineEM;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		$em = $this->doctrineEM;
		$data = $em->getRepository ( 'Application\Entity\NmtWfWorkflow' )->findAll ();
		foreach ( $data as $row ) {
			echo $row->getWorkflowName ();
			echo '<br />';
		}
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
			
			$vendor_name = $request->getPost ( 'vendor_name' );
			$vendor_short_name = $request->getPost ( 'vendor_short_name' );
			$keywords = $request->getPost ( 'keywords' );
			$country_id = $request->getPost ( 'country_id' );
			$is_active = $request->getPost ( 'is_active' );
			if ($is_active == null) :
				$is_active = 0;
			endif;
			
			$remarks = $request->getPost ( 'remarks' );
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			
			$errors = array ();
			
			$entity = new NmtBpVendor ();
			
			if ($vendor_name === '' or $vendor_name === null) {
				$errors [] = 'Please give vendor name';
			}
			
			if ($country_id === '' or $country_id === null) {
				$errors [] = 'Please give a country!';
				$country = null;
			} else {
				$country = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCountry', $country_id );
				$entity->setCountry ( $country );
			}
			$entity->setVendorName ( $vendor_name );
			$entity->setVendorShortName ( $vendor_short_name );
			$entity->setKeywords ( $keywords );
			$entity->setIsActive ( $is_active );
			$entity->setRemarks ( $remarks );
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'errors' => $errors,
						'redirectUrl' => $redirectUrl,
						'entity' => $entity,
						'country' => $country 
				
				) );
			}
			
			// No Error
			try {
				
				$entity->setCreatedOn ( new \DateTime () );
				$entity->setCreatedBy ( $u );
				
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
			} catch ( Exception $e ) {
				return new ViewModel ( array (
						'errors' => $e->getMessage (),
						'redirectUrl' => $redirectUrl,
						'entity' => null,
						'country' => null 
				
				) );
			}
			
			$this->flashMessenger ()->addSuccessMessage ( "Vendor " . $vendor_name . " has been created sucessfully" );
			return $this->redirect ()->toUrl ( $redirectUrl );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		return new ViewModel ( array (
				'errors' => null,
				'redirectUrl' => $redirectUrl,
				'entity' => null,
				'country' => null 
		
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
		
		$criteria1 = array ();
		if (! $is_active == null) {
			$criteria1 = array (
					"isActive" => $is_active 
			);
			
			if ($is_active == - 1) {
				$criteria1 = array (
						"isActive" => '0' 
				);
			}
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
		$context = $this->params ()->fromQuery ( 'context' );
		
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
				'paginator' => null,
				'context' =>$context,
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
