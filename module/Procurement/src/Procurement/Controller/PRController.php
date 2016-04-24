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


use Procurement\Model\PurchaseRequest;
use Procurement\Model\PurchaseRequestTable;

use Procurement\Model\PurchaseRequestItem;
use Procurement\Model\PurchaseRequestItemTable;

use Procurement\Model\PurchaseRequestItemPic;
use Procurement\Model\PurchaseRequestItemPicTable;

use User\Model\UserTable;

use Inventory\Services\SparePartsSearchService;
use Inventory\Model\MLASparepartTable;




class PRController extends AbstractActionController {
	
	protected  $userTable;
	protected  $purchaseRequestTable;
	protected  $purchaseRequestItemTable;
	protected  $purchaseRequestItemPicTable;
	protected  $sparePartTable;
	
	
	protected  $authService;
	protected  $sparepartSearchService;
	
	
	
	
	public function indexAction() {
		return new ViewModel ();
	}
	
	public function createStep1Action() {
		
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity();
		$user=$this->userTable->getUserByEmail($identity);
		
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
			
		if ($request->isPost ()) {
		
			if ($request->isPost ()) {
				$redirectUrl  = $request->getPost ( 'redirectUrl' );
		
				$input = new PurchaseRequest();
				$input->pr_number = $request->getPost ( 'pr_number' );
				$input->name = $request->getPost ( 'name' );
				$input->description = $request->getPost ( 'description' );				
				$input->requested_by = $user['id'];
				
				// validator.
				$errors = array();
		
				if ($input->pr_number ==''){
					$errors [] = 'Please give a PR number';
				}
					
		
				if (count ( $errors ) > 0) {
						return new ViewModel ( array (
							'redirectUrl'=>$redirectUrl,
							'user' =>$user,
							'errors' => $errors,
					) );
				}
		
				$newId = $this->purchaseRequestTable->add($input);
				$this->redirect()->toUrl('/procurement/pr/create-step2?pr_id='.$newId);
				
				
				}
		}
		
		
		return new ViewModel ( array (
				'redirectUrl'=>$redirectUrl,
				'user' =>$user,
				'errors' => null,
		));
		
	
	}
	
	public function createStep2Action() {
		
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity();
		$user=$this->userTable->getUserByEmail($identity);
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		$pr_id = $this->params ()->fromQuery ( 'pr_id' );
		$pr=$this->purchaseRequestTable->getPR($pr_id);
		$pr_items = $this->purchaseRequestItemTable->getItemsByPR($pr_id);
				
		return new ViewModel ( array (
				'redirectUrl'=>$redirectUrl,
				'user' =>$user,
				'errors' => null,
				'pr'=>$pr,
				'pr_items'=>$pr_items,
		));
	}
	
	public function addItemAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity();
		$user=$this->userTable->getUserByEmail($identity);
		
		if ($request->isPost ()) {
		
			if ($request->isPost ()) {
				$redirectUrl  = $request->getPost ( 'redirectUrl' );
				
				$input = new PurchaseRequestItem();
				$input->purchase_request_id = $request->getPost ( 'pr_id' );
				$input->priority = $request->getPost ( 'priority' );
				$input->name = $request->getPost ( 'name' );
				$input->description = $request->getPost ( 'description' );

				$input->unit = $request->getPost ( 'unit' );
				$input->quantity = $request->getPost ( 'quantity' );
				$input->EDT = $request->getPost ( 'EDT' );
				
				
					// validator.
				$errors = array();
		
				if ($input->name ==''){
					$errors [] = 'Please give a item name';
				}
				
				if ($input->unit ==''){
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
					
					$pr=$this->purchaseRequestTable->get($input->purchase_request_id);
					
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'user' => $user,
							'errors' => $errors,
							'pr' => $pr 
					) );
				}
		
				$this->purchaseRequestItemTable->add($input);
				$this->redirect()->toUrl($redirectUrl);
				}
		}
		
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		$pr_id = $this->params ()->fromQuery ( 'pr_id' );
		$pr=$this->purchaseRequestTable->get($pr_id);
		
				
		return new ViewModel ( array (
				'redirectUrl'=>$redirectUrl,
				'user' =>$user,
				'errors' => null,
				'pr'=>$pr,
		));
	}
	
	/* Request for new spare parts
	 * step1: search the spare part  
	 * 
	 */
	public function addItemSP1Action() {
		
		//$query = $this->params ()->fromQuery ( 'query' );
		
		$q = $this->params ()->fromQuery ( 'query' );
		$pr_id = $this->params ()->fromQuery ( 'pr_id' );
		$pr=$this->purchaseRequestTable->getPR	($pr_id);
		
		$json = (int) $this->params ()->fromQuery ( 'json' );
			
		if($q==''){
			return new ViewModel ( array (
					'hits' => null,
					'pr'=>$pr,
						
			));
		}
		
		if (strpos($q,'*') !== false) {
			$pattern = new Term($q);
			$query = new Wildcard($pattern);
			$hits = $this->sparepartSearchService->search($query);
		
		}else{
			$hits = $this->sparepartSearchService->search($q);
		}
		
		
		if ($json === 1){
		
			$data = array();
		
			foreach ($hits as $key => $value)
			{
				$n = (int)$key;
				$data[$n]['id'] = $value->sparepart_id;
				$data[$n]['name'] =  $value->name;
				$data[$n]['tag'] =  $value->tag;
				$data[$n]['code'] =  $value->code;
		
			}
		
		
			$response = $this->getResponse();
			$response->getHeaders()->addHeaderLine( 'Content-Type', 'application/json' );
			$response->setContent(json_encode($data));
			return $response;
		}
		
		return new ViewModel ( array (
				'query' => $q,
				'hits' => $hits,
				'pr'=>$pr,
				
		));
	}
	
	/* Request for new spare parts
	 * step1: search the spare part
	 *
	 */
	public function addItemSP2Action() {
	
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity();
		$user=$this->userTable->getUserByEmail($identity);
		
		if ($request->isPost ()) {
		
			if ($request->isPost ()) {
				$redirectUrl  = $request->getPost ( 'redirectUrl' );
				
				$input = new PurchaseRequestItem();
				$input->purchase_request_id = $request->getPost ( 'pr_id' );
				$input->priority = $request->getPost ( 'priority' );
				$input->name = $request->getPost ( 'name' );
				$input->description = $request->getPost ( 'description' );
				$input->code = $request->getPost ( 'code' );
				

				$input->unit = $request->getPost ( 'unit' );
				$input->quantity = $request->getPost ( 'quantity' );
				$input->EDT = $request->getPost ( 'EDT' );
				
				$input->sparepart_id = $request->getPost ( 'sparepart_id' );
				
				
					// validator.
				$errors = array();
		
				if ($input->name ==''){
					$errors [] = 'Please give a item name';
				}
				
				if ($input->unit ==''){
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
					
					$pr=$this->purchaseRequestTable->getPR($input->purchase_request_id);
					
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'user' => $user,
							'errors' => $errors,
							'pr' => $pr 
					) );
				}
		
				$this->purchaseRequestItemTable->add($input);
				$this->redirect()->toUrl($redirectUrl);
				}
		}
		
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		$pr_id = $this->params ()->fromQuery ( 'pr_id' );
		$pr=$this->purchaseRequestTable->getPR($pr_id);
		
		$sp_id = $this->params ()->fromQuery ( 'sparepart_id' );
		
		$sp =$this->sparePartTable->get($sp_id);
				
		return new ViewModel ( array (
				'redirectUrl'=>$redirectUrl,
				'user' =>$user,
				'errors' => null,
				'pr'=>$pr,
				'sp' =>$sp,
		));
	}
	
	public function myPRAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity();
		$user=$this->userTable->getUserByEmail($identity);
	
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		$my_pr=$this->purchaseRequestTable->getPRof($user['id']);
	
	
		return new ViewModel ( array (
				'redirectUrl'=>$redirectUrl,
				'user' =>$user,
				'errors' => null,
				'my_pr'=>$my_pr,
		));
	
	}
	
	public function showItemsAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity();
		$user=$this->userTable->getUserByEmail($identity);
	
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		$my_pr=$this->purchaseRequestTable->getPRof($user['id']);
	
	
		return new ViewModel ( array (
				'redirectUrl'=>$redirectUrl,
				'user' =>$user,
				'errors' => null,
				'my_pr'=>$my_pr,
		));
	
	}
	
	public function editItemAction() {
		return new ViewModel ();
	}
	
	public function deleteItemAction() {
		return new ViewModel ();
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
	
	
	

}
