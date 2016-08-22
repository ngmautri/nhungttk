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
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\Wildcard;
use MLA\Paginator;
use MLA\Files;
use Procurement\Model\PurchaseRequest;
use Procurement\Model\PurchaseRequestTable;
use Procurement\Model\PurchaseRequestItem;
use Procurement\Model\PurchaseRequestItemTable;
use Procurement\Model\PurchaseRequestItemPic;
use Procurement\Model\PurchaseRequestItemPicTable;
use User\Model\UserTable;
use Inventory\Services\SparePartsSearchService;
use Inventory\Model\MLASparepartTable;
use Inventory\Model\ArticleTable;
use Procurement\Model\PRWorkFlow;
use Procurement\Model\PRWorkFlowTable;
use Application\Model\DepartmentTable;
use Procurement\Model\PurchaseRequestCartItem;
use Procurement\Model\PurchaseRequestCartItemTable;
use Zend\Session\Container;

class PRController extends AbstractActionController {
	protected $userTable;
	protected $purchaseRequestTable;
	protected $purchaseRequestItemTable;
	protected $purchaseRequestCartItemTable;
	protected $purchaseRequestItemPicTable;
	protected $sparePartTable;
	protected $articleTable;
	protected $prWorkflowTable;
	protected $departmentTable;
	protected $authService;
	protected $sparepartSearchService;
	public function indexAction() {
		return new ViewModel ();
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createStep1Action() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		if ($request->isPost ()) {
			
			if ($request->isPost ()) {
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$input = new PurchaseRequest ();
				$input->pr_number = $request->getPost ( 'pr_number' );
				$input->name = $request->getPost ( 'name' );
				$input->description = $request->getPost ( 'description' );
				$input->requested_by = $user ['id'];
				
				// validator.
				$errors = array ();
				
				if ($input->pr_number == '') {
					$errors [] = 'Please give a PR number';
				}
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'user' => $user,
							'errors' => $errors 
					) );
				}
				
				$newId = $this->purchaseRequestTable->add ( $input );
				$this->redirect ()->toUrl ( '/procurement/pr/create-step2?pr_id=' . $newId );
			}
		}
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createStep2Action() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$pr_id = $this->params ()->fromQuery ( 'pr_id' );
		$pr = $this->purchaseRequestTable->getPR ( $pr_id );
		$pr_items = $this->purchaseRequestItemTable->getItemsByPR ( $pr_id );
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'pr' => $pr,
				'pr_items' => $pr_items 
		) );
	}
	
	/**
	 * Submit PR
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function submitAction() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		$pr_id = $this->params ()->fromQuery ( 'pr_id' );
		$input = new PRWorkFlow ();
		$input->status = "Submitted";
		$input->purchase_request_id = $pr_id;
		$input->updated_by = $user ['id'];
		
		$last_workflow_id = $this->prWorkflowTable->add ( $input );
		$this->purchaseRequestTable->updateLastWorkFlow ( $pr_id, $last_workflow_id );
		$this->redirect ()->toUrl ( '/procurement/pr/my-pr' );
	}
	public function addItemAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			if ($request->isPost ()) {
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$input = new PurchaseRequestItem ();
				$input->purchase_request_id = $request->getPost ( 'pr_id' );
				$input->priority = $request->getPost ( 'priority' );
				$input->name = $request->getPost ( 'name' );
				$input->description = $request->getPost ( 'description' );
				
				$input->unit = $request->getPost ( 'unit' );
				$input->quantity = $request->getPost ( 'quantity' );
				$input->EDT = $request->getPost ( 'EDT' );
				$input->created_by = $user ['id'];
				
				// validator.
				$errors = array ();
				
				if ($input->name == '') {
					$errors [] = 'Please give a item name';
				}
				
				if ($input->unit == '') {
					$errors [] = 'Please give a unit';
				}
				
				$validator = new Date ();
				
				if (! $validator->isValid ( $input->EDT )) {
					$errors [] = 'requested delievery date is not correct!';
				}
				
				// Fixed it by going to php.ini and uncommenting extension=php_intl.dll
				$validator = new Int ();
				
				if (! $validator->isValid ( $input->quantity )) {
					$errors [] = 'Quantity is not valid. It must be a number.';
				}
				
				if (count ( $errors ) > 0) {
					
					$pr = $this->purchaseRequestTable->get ( $input->purchase_request_id );
					
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'user' => $user,
							'errors' => $errors,
							'pr' => $pr 
					) );
				}
				
				$this->purchaseRequestItemTable->add ( $input );
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$pr_id = $this->params ()->fromQuery ( 'pr_id' );
		$pr = $this->purchaseRequestTable->get ( $pr_id );
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'pr' => $pr 
		) );
	}
	
	/*
	 * Request for new spare parts
	 * step1: search the spare part
	 *
	 */
	public function addItemSP1Action() {
		
		// $query = $this->params ()->fromQuery ( 'query' );
		$q = $this->params ()->fromQuery ( 'query' );
		$pr_id = $this->params ()->fromQuery ( 'pr_id' );
		$pr = $this->purchaseRequestTable->getPR ( $pr_id );
		
		$json = ( int ) $this->params ()->fromQuery ( 'json' );
		
		if ($q == '') {
			return new ViewModel ( array (
					'hits' => null,
					'pr' => $pr 
			)
			 );
		}
		
		if (strpos ( $q, '*' ) !== false) {
			$pattern = new Term ( $q );
			$query = new Wildcard ( $pattern );
			$hits = $this->sparepartSearchService->search ( $query );
		} else {
			$hits = $this->sparepartSearchService->search ( $q );
		}
		
		if ($json === 1) {
			
			$data = array ();
			
			foreach ( $hits as $key => $value ) {
				$n = ( int ) $key;
				$data [$n] ['id'] = $value->sparepart_id;
				$data [$n] ['name'] = $value->name;
				$data [$n] ['tag'] = $value->tag;
				$data [$n] ['code'] = $value->code;
			}
			
			$response = $this->getResponse ();
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
			$response->setContent ( json_encode ( $data ) );
			return $response;
		}
		
		return new ViewModel ( array (
				'query' => $q,
				'hits' => $hits,
				'pr' => $pr 
		)
		 );
	}
	
	/*
	 * Request for new spare parts
	 * step1: search the spare part
	 *
	 */
	public function addItemSP2Action() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			if ($request->isPost ()) {
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$input = new PurchaseRequestItem ();
				$input->purchase_request_id = $request->getPost ( 'pr_id' );
				$input->priority = $request->getPost ( 'priority' );
				$input->name = $request->getPost ( 'name' );
				$input->description = $request->getPost ( 'description' );
				$input->code = $request->getPost ( 'code' );
				
				$input->unit = $request->getPost ( 'unit' );
				$input->quantity = $request->getPost ( 'quantity' );
				$input->EDT = $request->getPost ( 'EDT' );
				
				$input->sparepart_id = $request->getPost ( 'sparepart_id' );
				$input->created_by = $user ['id'];
				
				// validator.
				$errors = array ();
				
				if ($input->name == '') {
					$errors [] = 'Please give a item name';
				}
				
				if ($input->unit == '') {
					$errors [] = 'Please give a unit';
				}
				
				$validator = new Date ();
				
				if (! $validator->isValid ( $input->EDT )) {
					$errors [] = 'requested delievery date is not correct!';
				}
				
				// Fixed it by going to php.ini and uncommenting extension=php_intl.dll
				$validator = new Int ();
				
				if (! $validator->isValid ( $input->quantity )) {
					$errors [] = 'Quantity is not valid. It must be a number.';
				}
				
				if (count ( $errors ) > 0) {
					
					$pr = $this->purchaseRequestTable->getPR ( $input->purchase_request_id );
					
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'user' => $user,
							'errors' => $errors,
							'pr' => $pr 
					) );
				}
				
				$this->purchaseRequestItemTable->add ( $input );
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$pr_id = $this->params ()->fromQuery ( 'pr_id' );
		$pr = $this->purchaseRequestTable->getPR ( $pr_id );
		
		$sp_id = $this->params ()->fromQuery ( 'sparepart_id' );
		$sp = $this->sparePartTable->get ( $sp_id );
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'pr' => $pr,
				'sp' => $sp 
		) );
	}
	
	/**
	 * Select Item from List
	 */
	public function selectItem1Action() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$user_id = $user ['id'];
		
		$pr_id = $this->params ()->fromQuery ( 'pr_id' );
		$pr = $this->purchaseRequestTable->getPR ( $pr_id );
		
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
		
		$articles = $this->articleTable->getArticles ( $user_id, 0, 0 );
		$totalResults = $articles->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$articles = $this->articleTable->getArticles ( $user_id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'total_articles' => $totalResults,
				'articles' => $articles,
				'pr' => $pr,
				'paginator' => $paginator 
		) );
	}
	
	/*
	 * Request for new spare parts
	 * step1: search the spare part
	 *
	 */
	public function selectItem2Action() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			if ($request->isPost ()) {
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$input = new PurchaseRequestItem ();
				$input->purchase_request_id = $request->getPost ( 'pr_id' );
				$input->priority = $request->getPost ( 'priority' );
				$input->name = $request->getPost ( 'name' );
				$input->description = $request->getPost ( 'description' );
				$input->code = $request->getPost ( 'code' );
				
				$input->unit = $request->getPost ( 'unit' );
				$input->quantity = $request->getPost ( 'quantity' );
				$input->EDT = $request->getPost ( 'EDT' );
				$input->article_id = $request->getPost ( 'article_id' );
				$input->remarks = $request->getPost ( 'remarks' );
				$input->created_by = $user ['id'];
				
				// validator.
				$errors = array ();
				
				if ($input->name == '') {
					$errors [] = 'Please give a item name';
				}
				
				if ($input->unit == '') {
					$errors [] = 'Please give a unit';
				}
				
				$validator = new Date ();
				
				if (! $validator->isValid ( $input->EDT )) {
					$errors [] = 'requested delievery date is not correct!';
				}
				
				// Fixed it by going to php.ini and uncommenting extension=php_intl.dll
				$validator = new Int ();
				
				if (! $validator->isValid ( $input->quantity )) {
					$errors [] = 'Quantity is not valid. It must be a number.';
				}
				
				if (count ( $errors ) > 0) {
					
					$pr = $this->purchaseRequestTable->getPR ( $input->purchase_request_id );
					
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'user' => $user,
							'errors' => $errors,
							'pr' => $pr 
					) );
				}
				
				$this->purchaseRequestItemTable->add ( $input );
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$pr_id = $this->params ()->fromQuery ( 'pr_id' );
		$pr = $this->purchaseRequestTable->getPR ( $pr_id );
		
		$article_id = $this->params ()->fromQuery ( 'article_id' );
		$article = $this->articleTable->get ( $article_id );
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'pr' => $pr,
				'article' => $article 
		) );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function myPRAction() {
		
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 15;
		} else {
			$resultsPerPage = $this->params ()->fromQuery ( 'perPage' );
		}
		
		if (is_null ( $this->params ()->fromQuery ( 'page' ) )) {
			$page = 1;
		} else {
			$page = $this->params ()->fromQuery ( 'page' );
		}
		
		$pr_status = $this->params ()->fromQuery ( 'pr_status' );
		$pr_year = $this->params ()->fromQuery ( 'pr_year' );
		$order_by = $this->params ()->fromQuery ( 'pr_year' );
		
		// all my PR
		$my_pr = $this->purchaseRequestTable->getMyPR ( $user ['id'], $pr_status, 0, 0, null );
		$totalResults = count ( $my_pr );
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$my_pr = $this->purchaseRequestTable->getMyPR ( $user ['id'], $pr_status, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1, $order_by );
		}
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'my_pr' => $my_pr,
				'paginator' => $paginator,
				'total_items' => $totalResults,
				'pr_status' => $pr_status,
				'pr_year' => $pr_year,
				'order_by' => $order_by 
		)
		 );
	}
	public function mySubmittedItemsAction() {
		
		// $request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$pr_items = $this->purchaseRequestItemTable->getMySubmittedPRItems ( $user ['id'] );
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'pr_items' => $pr_items 
		) );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function allPRAction() {
	
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$last_status = $this->params ()->fromQuery ( 'last_status' );
		$department_id = $this->params ()->fromQuery ( 'department_id' );
		$departments = $this->departmentTable->fetchAll ();
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 18;
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
		
		if ($last_status == null) :
			$last_status = '';
		endif;
		
		if ($department_id == null) :
			$department_id = 0;
		endif;
		
		$all_pr = $this->purchaseRequestTable->getPurchaseRequests ( $last_status, $department_id, 0, 0 );
		$totalResults = count ( $all_pr );
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$all_pr = $this->purchaseRequestTable->getPurchaseRequests ( $last_status, $department_id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'all_pr' => $all_pr,
				'departments' => $departments,
				'last_status' => $last_status,
				'department_id' => $department_id,
				'paginator' => $paginator,
				'total_items' => $totalResults 
		)
		 );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function approveStep1Action() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$pr_id = $this->params ()->fromQuery ( 'pr_id' );
		$pr = $this->purchaseRequestTable->getPR ( $pr_id );
		
		$balance = $this->params ()->fromQuery ( 'balance' );
		$unconfirmed_quantity = $this->params ()->fromQuery ( 'unconfirmed_quantity' );
		$added_delivery_list = $this->params ()->fromQuery ( 'added_delivery_list' );
		
		
		if ($balance == null) :
			$balance = 2;
		endif;
		
		if ($unconfirmed_quantity == null) :
		$unconfirmed_quantity = 2;
		endif;
		
		if ($added_delivery_list == null) :
		$added_delivery_list = 2;
		endif;
		
		
		$pr_items = $this->purchaseRequestItemTable->getPRItemsWithLastDN_V3( $pr_id,$balance,$unconfirmed_quantity,$added_delivery_list,0,0);
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'pr' => $pr,
				'pr_items' => $pr_items,
				'paginator'=>null,
				'balance' => $balance,
				'unconfirmed_quantity' => $unconfirmed_quantity,
				'added_delivery_list' => $added_delivery_list,
				
		) );
	}
	
	/**
	 * Submit PR
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function approveAction() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		$pr_id = $this->params ()->fromQuery ( 'pr_id' );
		$input = new PRWorkFlow ();
		$input->status = "Approved";
		$input->purchase_request_id = $pr_id;
		$input->updated_by = $user ['id'];
		
		$last_workflow_id = $this->prWorkflowTable->add ( $input );
		$this->purchaseRequestTable->updateLastWorkFlow ( $pr_id, $last_workflow_id );
		
		$this->redirect ()->toUrl ( '/procurement/pr/all-pr' );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function prItemsAction() {
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
		
		// $request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$last_status = $this->params ()->fromQuery ( 'last_status' );
		$department_id = $this->params ()->fromQuery ( 'department_id' );
		$balance = $this->params ()->fromQuery ( 'balance' );
		$unconfirmed_quantity = $this->params ()->fromQuery ( 'unconfirmed_quantity' );
		$added_delivery_list = $this->params ()->fromQuery ( 'added_delivery_list' );
		
		
		$departments = $this->departmentTable->fetchAll ();
		
		if ($balance == null) :
			$balance = 1;
		endif;
		
		if ($unconfirmed_quantity == null) :
			$unconfirmed_quantity = 2;
		endif;
		
		if ($added_delivery_list == null) :
			$added_delivery_list = 0;
		endif;
		
		
		$pr_items = $this->purchaseRequestItemTable->getPRItemsWithLastDN_V2( $department_id, $last_status, $balance, $unconfirmed_quantity,$added_delivery_list,0, 0 );
		$totalResults = count ( $pr_items );
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$pr_items = $this->purchaseRequestItemTable->getPRItemsWithLastDN_V2 ( $department_id, $last_status, $balance,$unconfirmed_quantity, $added_delivery_list,($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'pr_items' => $pr_items,
				'departments' => $departments,
				'last_status' => $last_status,
				'balance' => $balance,
				'unconfirmed_quantity' => $unconfirmed_quantity,
				'added_delivery_list' => $added_delivery_list,
				
				'department_id' => $department_id,
				'paginator' => $paginator,
				'total_items' => $totalResults 
		)
		 );
	}
	
	/*
	 * Request for new spare parts
	 * step1: search the spare part
	 *
	 */
	public function addPRCartAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			if ($request->isPost ()) {
				// $redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$item_type = $request->getPost ( 'item_type' );
				$item_id = $request->getPost ( 'item_id' );
				
				$input = new PurchaseRequestCartItem ();
				
				$input->priority = $request->getPost ( 'priority' );
				$input->name = $request->getPost ( 'name' );
				$input->code = $request->getPost ( 'code' );
			
				$input->quantity = $request->getPost ( 'quantity' );
				$input->EDT = $request->getPost ( 'EDT' );
				$input->unit = $request->getPost ( 'unit' );
				$input->remarks = $request->getPost ( 'remarks' );
				$input->created_by = $user ['id'];
				
				switch ($item_type) {
					case "ARTICLE" :
						$input->article_id = $item_id;
						break;
					case "SPARE-PART" :
						$input->sparepart_id = $item_id;
						break;
				}
				
				// validator.
				$errors = array ();
				
				$validator = new Date ();
				
				if (! $validator->isValid ( $input->EDT )) {
					$errors [] = 'requested delievery date is not correct!';
				} else {
					$today = date ( "Y-m-d H:i:s" );
					if ($input->EDT < $today) {
						$errors [] = 'requested delievery date is in the past!';
					}
				}
				// Fixed it by going to php.ini and uncommenting extension=php_intl.dll
				$validator = new Int ();
				
				if (! $validator->isValid ( $input->quantity )) {
					$errors [] = 'Quantity is not valid. It must be a number.';
				}
				
				$response = $this->getResponse ();
				
				if (count ( $errors ) > 0) {
					
					$c = array (
							'status' => '0',
							'messages' => $errors 
					);
					
					$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
					$response->setContent ( json_encode ( $c ) );
					return $response;
				}
				
				$this->purchaseRequestCartItemTable->add ( $input );
				$c = array (
						'status' => '1',
						'messages' => null 
				);
				
				$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
				$response->setContent ( json_encode ( $c ) );
				return $response;
			}
		}
	}
	
	/*
	 * Request for new spare parts
	 * step1: search the spare part
	 *
	 */
	public function updateCartAction() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$total_cart_items = $this->purchaseRequestCartItemTable->getTotalCartItems ( $user ['id'] );
		
		$session = new Container ( 'MLA_USER' );
		$session->offsetSet ( 'cart_items', $total_cart_items );
		
		$c = array (
				'total_cart_items' => $total_cart_items 
		);
		
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
		$response->setContent ( json_encode ( $c ) );
		return $response;
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function cartAction() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$cart_items = $this->purchaseRequestCartItemTable->getCartItems ( $user ['id'] );
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'cart_items' => $cart_items 
		) );
	}
	
	/**
	 * Submit PR
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function submitCartItemsAction() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$user_id = $user ['id'];
		$pr_number = $this->params ()->fromQuery ( 'pr_number' );
		$select_all_item = $this->params ()->fromQuery ( 'SelectAll' );
		
		// create PR
		$pr = new PurchaseRequest ();
		
		$t = $this->purchaseRequestTable->getTotalPROfYear ( $user_id );
		if ($t == null) :
			$pr->seq_number_of_year = 1;
			$pr->auto_pr_number = "PR-000" . $pr->seq_number_of_year;
		 else :
			$pr->seq_number_of_year = $t->total_pr_this_year + 1;
			$pr->auto_pr_number = "PR-" . $t->pr_of_department_short_name . "-000" . $pr->seq_number_of_year;
		endif;
		
		// $pr->seq_number_year = 1;
		// $pr->auto_pr_number ="PR-". $pr->seq_number_year;
		
		$pr->pr_number = $pr_number;
		$pr->requested_by = $user_id;
		$pr_id = $this->purchaseRequestTable->add ( $pr );
		
		// update PR Workflow
		$input = new PRWorkFlow ();
		$input->status = "Submitted";
		$input->purchase_request_id = $pr_id;
		$input->updated_by = $user ['id'];
		
		$last_workflow_id = $this->prWorkflowTable->add ( $input );
		$this->purchaseRequestTable->updateLastWorkFlow ( $pr_id, $last_workflow_id );
		
		// if user submit all items*/
		if ($select_all_item == "YES") {
			
			// add PR Items from ALL Cart Items
			$this->purchaseRequestCartItemTable->submitCartItems ( $user_id, $pr_id );
			
			// update cart item status
			$this->purchaseRequestCartItemTable->setCartItemsAsOrdered ( $user_id );
			
			$session = new Container ( 'MLA_USER' );
			$session->offsetSet ( 'cart_items', 0 );
		} else {
			
			$selected_items = $this->params ()->fromQuery ( 'cart_items' );
			
			// add PR Items from SELETECT Cart Items
			$this->purchaseRequestCartItemTable->submitSelectedCartItems ( $selected_items, $pr_id );
			
			// update cart item status
			$this->purchaseRequestCartItemTable->setSelectedCartItemsAsOrdered ( $selected_items );
			
			$total_cart_items = $this->purchaseRequestCartItemTable->getTotalCartItems ( $user ['id'] );
			
			$session = new Container ( 'MLA_USER' );
			$session->offsetSet ( 'cart_items', $total_cart_items );
		}
		
		$this->redirect ()->toUrl ( '/procurement/pr/my-pr' );
	}
	
	/**
	 * AJAX
	 */
	public function deleteCartItemAction() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$this->purchaseRequestCartItemTable->delete ( $id );
		
		$total_cart_items = $this->purchaseRequestCartItemTable->getTotalCartItems ( $user ['id'] );
		
		$session = new Container ( 'MLA_USER' );
		$session->offsetSet ( 'cart_items', $total_cart_items );
		
		$c = array (
				'status' => $id . ' deleted' 
		);
		
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
		$response->setContent ( json_encode ( $c ) );
		return $response;
	}
	public function addItemPictureAction() {
		return new ViewModel ();
	}
	public function showAction() {
		return new ViewModel ();
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
	public function setUserTable(UserTable $userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getPurchaseRequestTable() {
		return $this->purchaseRequestTable;
	}
	public function setPurchaseRequestTable(PurchaseRequestTable $purchaseRequestTable) {
		$this->purchaseRequestTable = $purchaseRequestTable;
		return $this;
	}
	public function getPurchaseRequestItemTable() {
		return $this->purchaseRequestItemTable;
	}
	public function setPurchaseRequestItemTable(PurchaseRequestItemTable $purchaseRequestItemTable) {
		$this->purchaseRequestItemTable = $purchaseRequestItemTable;
		return $this;
	}
	public function getPurchaseRequestItemPicTable() {
		return $this->purchaseRequestItemPicTable;
	}
	public function setPurchaseRequestItemPicTable(PurchaseRequestItemPicTable $purchaseRequestItemPicTable) {
		$this->purchaseRequestItemPicTable = $purchaseRequestItemPicTable;
		return $this;
	}
	public function getSparepartSearchService() {
		return $this->sparepartSearchService;
	}
	public function setSparepartSearchService(SparePartsSearchService $sparepartSearchService) {
		$this->sparepartSearchService = $sparepartSearchService;
		return $this;
	}
	public function getSparePartTable() {
		return $this->sparePartTable;
	}
	public function setSparePartTable(MLASparepartTable $sparePartTable) {
		$this->sparePartTable = $sparePartTable;
		return $this;
	}
	public function getArticleTable() {
		return $this->articleTable;
	}
	public function setArticleTable(ArticleTable $articleTable) {
		$this->articleTable = $articleTable;
		return $this;
	}
	public function getPrWorkflowTable() {
		return $this->prWorkflowTable;
	}
	public function setPrWorkflowTable(PRWorkFlowTable $prWorkflowTable) {
		$this->prWorkflowTable = $prWorkflowTable;
		return $this;
	}
	public function getDepartmentTable() {
		return $this->departmentTable;
	}
	public function setDepartmentTable(DepartmentTable $departmentTable) {
		$this->departmentTable = $departmentTable;
		return $this;
	}
	public function getPurchaseRequestCartItemTable() {
		return $this->purchaseRequestCartItemTable;
	}
	public function setPurchaseRequestCartItemTable(PurchaseRequestCartItemTable $purchaseRequestCartItemTable) {
		$this->purchaseRequestCartItemTable = $purchaseRequestCartItemTable;
		return $this;
	}

}
