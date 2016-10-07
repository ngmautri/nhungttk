<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Procurement\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\I18n\Validator\Int;
use User\Model\UserTable;
use MLA\Paginator;
use MLA\Files;
use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\MultiTerm;
use ZendSearch\Lucene\Search\Query\Wildcard;
use Procurement\Services\VendorSearchService;
use \Exception;
use Procurement\Model\Vendor;
use Procurement\Model\VendorTable;

/**
 *
 * @author nmt
 *        
 */
class VendorController extends AbstractActionController {
	protected $authService;
	protected $userTable;
	protected $vendorTable;
	protected $vendorSearchService;
	public function indexAction() {
		return new ViewModel ();
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel|\Zend\Stdlib\ResponseInterface
	 */
	public function searchAction() {
		$q = $this->params ()->fromQuery ( 'query' );
		$json = ( int ) $this->params ()->fromQuery ( 'json' );
		
		if ($q == '') {
			return new ViewModel ( array (
					'hits' => null 
			) );
		}
		
		$error = null;
		try {
			
			if (strpos ( $q, '*' ) != false) {
				$pattern = new Term ( $q );
				$query = new Wildcard ( $pattern );
				$hits = $this->vendorSearchService->search ( $query );
			} else {
				$hits = $this->vendorSearchService->search ( $q );
			}
		} catch ( Exception $e ) {
			$error = $e->getMessage ();
			$hits=null;
		}
		
		if ($json === 1) {
			$data = array ();
			if ($hits != null) {
				foreach ( $hits as $key => $value ) {
					$n = ( int ) $key;
					$data [$n] ['id'] = $value->vendor_id;
					$data [$n] ['name'] = $value->name;
					$data [$n] ['keywords'] = $value->keywords;
				}
			}
			$response = $this->getResponse ();
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
			$response->setContent ( json_encode ( $data ) );
			return $response;
		}
		
		return new ViewModel ( array (
				'query' => $q,
				'hits' => $hits,
				'error' => $error,
		) );
	}
	
	/**
	 * create New Article
	 */
	public function addAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			if ($request->isPost ()) {
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$input = new Vendor ();
				$input->created_by = $user ['id'];
				
				$input->name = $request->getPost ( 'name' );
				$input->description = $request->getPost ( 'description' );
				
				$input->keywords = $request->getPost ( 'keywords' );
				
				$input->visibility = $request->getPost ( 'visibility' );
				$input->status = $request->getPost ( 'status' );
				
				$errors = array ();
				
				if ($input->name == '') {
					$errors [] = 'Please give vendor name';
				}
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							'errors' => $errors,
							'redirectUrl' => $redirectUrl,
							'vendor' => $input 
					) );
				}
				
				$this->vendorTable->add ( $input );
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		return new ViewModel ( array (
				'message' => 'Add new article',
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'vendor' => null 
		) );
	}
	
	/**
	 * List vendor
	 */
	public function listAction() {
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 20;
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
		
		$vendors = $this->vendorTable->getVendors();
		$totalResults = $vendors->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$vendors = $this->vendorTable->getVendors();
		}
		
		return new ViewModel ( array (
				'total_articles' => $totalResults,
				'vendors' => $vendors,
				'paginator' => $paginator 
		) );
	}
	
	/**
	 * List all articles
	 */
	public function listJsonAction() {
		$vendors = $this->vendorTable->getVendors ();
		$data = array ();
		foreach ( $vendors as $key => $value ) {
			$n = ( int ) $key;
			$data [$n] ['id'] = $value->id;
			$data [$n] ['name'] = $value->name;
			$data [$n] ['keywords'] = $value->keywords;
		}
		
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
		$response->setContent ( json_encode ( $data ) );
		return $response;
	}
	
	/**
	 * Show detail of Vendor
	 */
	public function showAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$vendor = $this->vendorTable->get ( $id );
		
		return new ViewModel ( array (
				'vendor' => $vendor 
		) );
	}
	public function getAuthService() {
		return $this->authService;
	}
	public function setAuthService($authService) {
		$this->authService = $authService;
		return $this;
	}
	public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable($userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getVendorTable() {
		return $this->vendorTable;
	}
	public function setVendorTable(VendorTable $vendorTable) {
		$this->vendorTable = $vendorTable;
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
