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
use MLA\Paginator;
use MLA\Files;
use Procurement\Model\PurchaseRequest;
use Procurement\Model\PurchaseRequestTable;
use Procurement\Model\PurchaseRequestItem;
use Procurement\Model\PurchaseRequestItemTable;
use Procurement\Model\PurchaseRequestItemPic;
use Procurement\Model\PurchaseRequestItemPicTable;
use Procurement\Model\Delivery;
use Procurement\Model\DeliveryTable;
use Procurement\Model\DeliveryItem;
use Procurement\Model\DeliveryItemTable;
use Procurement\Model\PRItemWorkFlow;
use Procurement\Model\PRItemWorkFlowTable;
use Procurement\Model\DeliveryWorkFlow;
use Procurement\Model\DeliveryWorkFlowTable;
use Application\Model\DepartmentTable;
use User\Model\UserTable;

/**
 *
 * @author nmt
 *        
 */
class DeliveryController extends AbstractActionController {
	protected $userTable;
	protected $purchaseRequestItemTable;
	protected $purchaseRequestTable;
	protected $deliveryTable;
	protected $deliveryItemTable;
	protected $deliveryWorkFlowTable;
	
	protected $departmentTable;
	protected $PRItemWorkflowTable;
	protected $authService;
	public function indexAction() {
		return new ViewModel ();
	}
	public function createStep1Action() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		if ($request->isPost ()) {
			
			if ($request->isPost ()) {
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$input = new Delivery ();
				$input->dn_number = $request->getPost ( 'dn_number' );
				$input->description = $request->getPost ( 'description' );
				$input->created_by = $user ['id'];
				
				// validator.
				$errors = array ();
				
				if ($input->dn_number == '') {
					$errors [] = 'Please give a deliver number';
				}
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'user' => $user,
							'errors' => $errors 
					) );
				}
				
				$newId = $this->deliveryTable->add ( $input );
				$this->redirect ()->toUrl ( '/procurement/delivery/create-step2?dn_id=' . $newId );
			}
		}
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null 
		) );
	}
	public function createStep2Action() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$dn_id = ( int ) $this->params ()->fromQuery ( 'dn_id' );
		$dn = $this->deliveryTable->getDeliveries( $dn_id,0,0,0)->current();
		$dn_items = $this->deliveryItemTable->getItemsByDN ( $dn_id );
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'dn' => $dn,
				'dn_items' => $dn_items 
		) );
	}
	
	/**
	 * Ajax
	 * ================================
	 */
	public function completeNotifyAction() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		$dn_id = $this->params ()->fromQuery ( 'dn_id' );
		$dn_items = $this->deliveryTable->getDeliveryItems ( $dn_id );
		
		if (count ( $dn_items ) > 0) :
			$wf = new DeliveryWorkFlow();
			$wf->delivery_id = $dn_id;
			$wf->status = "Notified";
			$wf->updated_by = $user ['id'];
				$this->deliveryWorkFlowTable->add($wf);
		
			
			foreach ( $dn_items as $dn_item ) {
				$input = new PRItemWorkFlow ();
				$input->delivery_id = $dn_id;
				$input->status = "Notified";
				$input->pr_item_id = $dn_item->pr_item_id;
				$input->updated_by = $user ['id'];
				$this->PRItemWorkflowTable->add ( $input );
			}
		
		endif;
		
		$this->redirect ()->toUrl ( '/procurement/pr/all-pr' );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function selectFromList1Action() {
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
		
		$dn_id = $this->params ()->fromQuery ( 'dn_id' );
		$dn = $this->deliveryTable->get ( $dn_id );
		
		$last_status = $this->params ()->fromQuery ( 'last_status' );
		$user_id = $this->params ()->fromQuery ( 'user_id' );
		$department_id = $this->params ()->fromQuery ( 'department_id' );
		$departments = $this->departmentTable->fetchAll ();
		
		if ($user_id == null) :
			$user_id = '';
		
		endif;
		
		if ($last_status == null) :
			$last_status = '';
		
		endif;
		
		if ($department_id == null) :
			$department_id = '';
		
		endif;
		
		$pr_items = $this->purchaseRequestItemTable->getAllSubmittedPRItems ( $last_status, $user_id, $department_id, 1, 0, 0 );
		$totalResults = count ( $pr_items );
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$pr_items = $this->purchaseRequestItemTable->getAllSubmittedPRItems ( $last_status, $user_id, $department_id, 1, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'pr_items' => $pr_items,
				'departments' => $departments,
				'last_status' => $last_status,
				'department_id' => $department_id,
				'paginator' => $paginator,
				'total_items' => $totalResults,
				'dn' => $dn 
		) );
	}
	
	/*
	 *
	 */
	public function selectFromList2Action() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		if ($request->isPost ()) {
			
			if ($request->isPost ()) {
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$input = new DeliveryItem ();
				$input->delivery_id = $request->getPost ( 'delivery_id' );
				$input->pr_item_id = $request->getPost ( 'pr_item_id' );
				
				$input->name = $request->getPost ( 'name' );
				$input->code = $request->getPost ( 'unit' );
				$input->unit = $request->getPost ( 'unit' );
				
				$input->vendor_id = $request->getPost ( 'vendor_id' );
				
				$input->delivered_quantity = $request->getPost ( 'delivered_quantity' );
				$input->price = $request->getPost ( 'price' );
				$input->currency = $request->getPost ( 'curreny' );
				
				$input->remarks = $request->getPost ( 'remarks' );
				$input->created_by = $user ['id'];
				
				$pr_item = $this->purchaseRequestItemTable->getItem ( $input->pr_item_id );
				$to_delivery = $pr_item->quantity - $pr_item->delivered_quantity;
				
				// validator.
				$errors = array ();
				
				if ($input->delivery_id == '') {
					$errors [] = 'Please give a deliver number';
				}
				
				// Fixed it by going to php.ini and uncommenting extension=php_intl.dll
				$validator = new Int ();
				
				if (! $validator->isValid ( $input->delivered_quantity )) {
					$errors [] = 'Deliver Quantity is not valid. It must be a number.';
				} else {
					
					if ($input->delivered_quantity > $to_delivery) {
						$errors [] = 'Deliver quantity is: ' . $input->delivered_quantity . ' pcs, which is bigger than amount to delivery';
					}
				}
				
				if (! is_numeric ( $input->price )) {
					$errors [] = 'Price is not valid. It must be a number.';
				}
				
				if (count ( $errors ) > 0) {
					$dn = $this->deliveryTable->get ( $input->delivery_id );
					$pr_item = $this->purchaseRequestItemTable->getItem ( $input->pr_item_id );
					
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'user' => $user,
							'errors' => $errors,
							'dn' => $dn,
							'pr_item' => $pr_item 
					)
					 );
				}
				
				$this->deliveryItemTable->add ( $input );
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$dn_id = ( int ) $this->params ()->fromQuery ( 'dn_id' );
		$dn = $this->deliveryTable->get ( $dn_id );
		
		$pr_item_id = ( int ) $this->params ()->fromQuery ( 'pr_item_id' );
		$pr_item = $this->purchaseRequestItemTable->getItem ( $pr_item_id );
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'dn' => $dn,
				'pr_item' => $pr_item 
		) );
	}
	
	/**
	 * get My Delivery
	 */
	public function myDeliveryAction() {
			$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$my_deliveries = $this->deliveryTable->getDeliveries(0,$user ['id'],0,0);
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'my_delivery' => $my_deliveries 
		) );
	}
	
	
	public function addItemAction() {
		return new ViewModel ();
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
	public function getAuthService() {
		return $this->authService;
	}
	public function setAuthService($authService) {
		$this->authService = $authService;
		return $this;
	}
	public function getDeliveryTable() {
		return $this->deliveryTable;
	}
	public function setDeliveryTable(DeliveryTable $deliveryTable) {
		$this->deliveryTable = $deliveryTable;
		return $this;
	}
	public function getDeliveryItemTable() {
		return $this->deliveryItemTable;
	}
	public function setDeliveryItemTable(DeliveryItemTable $deliveryItemTable) {
		$this->deliveryItemTable = $deliveryItemTable;
		return $this;
	}
	public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable(UserTable $userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getPurchaseRequestItemTable() {
		return $this->purchaseRequestItemTable;
	}
	public function setPurchaseRequestItemTable(PurchaseRequestItemTable $purchaseRequestItemTable) {
		$this->purchaseRequestItemTable = $purchaseRequestItemTable;
		return $this;
	}
	public function getPurchaseRequestTable() {
		return $this->purchaseRequestTable;
	}
	public function setPurchaseRequestTable(PurchaseRequestTable $purchaseRequestTable) {
		$this->purchaseRequestTable = $purchaseRequestTable;
		return $this;
	}
	public function getPRItemWorkflowTable() {
		return $this->PRItemWorkflowTable;
	}
	public function setPRItemWorkflowTable(PRItemWorkFlowTable $PRItemWorkflowTable) {
		$this->PRItemWorkflowTable = $PRItemWorkflowTable;
		return $this;
	}
	public function getDepartmentTable() {
		return $this->departmentTable;
	}
	public function setDepartmentTable($departmentTable) {
		$this->departmentTable = $departmentTable;
		return $this;
	}
	public function getDeliveryWorkFlowTable() {
		return $this->deliveryWorkFlowTable;
	}
	public function setDeliveryWorkFlowTable(DeliveryWorkFlowTable $deliveryWorkFlowTable) {
		$this->deliveryWorkFlowTable = $deliveryWorkFlowTable;
		return $this;
	}
	
	
}
