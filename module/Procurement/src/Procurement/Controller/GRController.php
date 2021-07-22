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

use Application\Domain\Util\Pagination\Paginator;
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
use Procurement\Model\DeliveryItemWorkFlow;
use Procurement\Model\DeliveryItemWorkFlowTable;
use Procurement\Model\DeliveryCart;
use Procurement\Model\DeliveryCartTable;
use Procurement\Model\POItem;
use Procurement\Model\POItemTable;
use Application\Model\DepartmentTable;
use User\Model\UserTable;
use Inventory\Model\ArticleMovement;
use Inventory\Model\ArticleMovementTable;
use Inventory\Model\ArticleLastDN;
use Inventory\Model\ArticleLastDNTable;
use Inventory\Model\SparepartMovement;
use Inventory\Model\SparepartMovementsTableTable;
use Inventory\Model\SparepartLastDN;
use Inventory\Model\SparepartLastDNTableDNTable;

/**
 *
 * @author nmt
 *        
 */
class GRController extends AbstractActionController {
	protected $userTable;
	protected $purchaseRequestItemTable;
	protected $purchaseRequestTable;
	protected $deliveryTable;
	protected $deliveryItemTable;
	protected $deliveryWorkFlowTable;
	protected $deliveryItemWorkFlowTable;
	protected $articleMovementTable;
	protected $articleLastDNTable;
	protected $sparepartMovementTable;
	protected $sparepartLastDNTable;
	protected $departmentTable;
	protected $PRItemWorkflowTable;
	protected $deliveryCartTable;
	protected $authService;
	protected $poItemTable;
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		return new ViewModel ();
	}
	
	/**
	 * Receive goods.
	 * this action will be done by procurement staff, when the good is purchased and arrived at MLA with invoice /pro-formal invoice
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function receiveAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$input = new DeliveryItem ();
			
			$input->receipt_date = $request->getPost ( 'receipt_date' );
			// $input->delivery_id = $request->getPost ( 'delivery_id' );
			
			$po_item_id = $request->getPost ( 'po_item_id' );
			$input->po_item_id = $po_item_id;
			$input->pr_item_id = $request->getPost ( 'pr_item_id' );
			
			$input->name = $request->getPost ( 'name' );
			$input->code = $request->getPost ( 'code' );
			$input->unit = $request->getPost ( 'unit' );
			
			$input->vendor_id = $request->getPost ( 'vendor_id' );
			
			$input->delivered_quantity = $request->getPost ( 'delivered_quantity' );
			$input->price = $request->getPost ( 'price' );
			$input->currency = $request->getPost ( 'curreny' );
			$input->payment_method = $request->getPost ( 'payment_method' );
			
			$input->remarks = $request->getPost ( 'remarks' );
			$input->created_by = $user ['id'];
			
			$input->invoice_no = $request->getPost ( 'invoice_no' );
			$input->invoice_date = $request->getPost ( 'invoice_date' );
					
			$pr_item = $this->purchaseRequestItemTable->getPRItem ( $input->pr_item_id );
			
			// validator.
			$errors = array ();
			
			
			// validator.
			$validator = new Date ();
			
			if (! $validator->isValid ( $input->receipt_date )) {
				$errors [] = 'Receipt date format is not correct!';
			}
			
			if (! $validator->isValid ( $input->invoice_date )) {
				$errors [] = 'Invoice date format is not correct!';
			}
			
			// Fixed it by going to php.ini and uncommenting extension=php_intl.dll
			$validator = new Int ();
			
			if (! $validator->isValid ( $input->delivered_quantity )) {
				$errors [] = 'Received quantity is not valid. It must be a number.';
			} else {
				if ($input->delivered_quantity < 0) {
					$errors [] = 'Received quantity must greater than 0';
				}
			}
			
			if (! is_numeric ( $input->price )) {
				$errors [] = 'Price is not valid. It must be a number.';
			}else {
				if ($input->price <= 0) {
					$errors [] = 'Price must be greater than 0!';
				}
			}
			
			if ($input->currency =="") {
				$errors [] = 'Please select currency!';
			}
			
			if ($input->payment_method =="") {
				$errors [] = 'Please select payment method!';
			}
			
			if (count ( $errors ) > 0) {
				$pr_item = $this->purchaseRequestItemTable->getPRItem ( $input->pr_item_id );
				$po_item = $this->poItemTable->getPOItem ( $input->po_item_id );
				
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'user' => $user,
						'errors' => $errors,
						'pr_item' => $pr_item,
						'po_item' => $po_item,
						'submitted_po_item' => $input,
						'vendor_name' => $request->getPost ( 'vendor' ),
				) );
			}
			
			$this->deliveryItemTable->add ( $input );
			$this->redirect ()->toUrl ( $redirectUrl );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$po_item_id = ( int ) $this->params ()->fromQuery ( 'po_item_id' );
		$pr_item_id = ( int ) $this->params ()->fromQuery ( 'pr_item_id' );
		
		$pr_item = $this->purchaseRequestItemTable->getPRItem ( $pr_item_id );
		$po_item = $this->poItemTable->getPOItem ( $po_item_id );
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'pr_item' => $pr_item,
				'po_item' => $po_item,
				'vendor_name' => $po_item->vendor_name,
				'submitted_po_item' => null,
		) );
	}
	
	/**
	 * get Receipts List of PO Items
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function receiveListAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'po_item_id' );
		
		$receipts = $this->deliveryItemTable->getDOItemsByPOItem ( $id );
		
		$department_id = $this->params ()->fromQuery ( 'department_id' );
		$balance = $this->params ()->fromQuery ( 'balance' );
		$notified = $this->params ()->fromQuery ( 'notified' );
		$notified_quantity = $this->params ()->fromQuery ( 'notified_quantity' );
		
		$departments = $this->departmentTable->fetchAll ();
		
		return new ViewModel ( array (
				'receipts' => $receipts,
				'departments' => $departments,
				'department_id' => $department_id,
				
				'balance' => $balance,
				'notified' => $notified,
				'notified_quantity' => $notified_quantity 
		)
		 );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
		$department_id = $this->params ()->fromQuery ( 'department_id' );
		$departments = $this->departmentTable->fetchAll ();
		$balance = $this->params ()->fromQuery ( 'balance' );
		$notified = $this->params ()->fromQuery ( 'notified' );
		$notified_quantity = $this->params ()->fromQuery ( 'notified_quantity' );
		
		if ($notified == null) :
			$notified = 0;
		
		endif;
		
		if ($balance == null) :
			$balance = 1;
		
		endif;
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 10;
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
		
		$receipts = $this->deliveryItemTable->getGRItems_V1 ( $balance, $notified, $notified_quantity, 0, 0 );
		$totalResults = count ( $receipts );
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$receipts = $this->deliveryItemTable->getGRItems_V1 ( $balance, $notified, $notified_quantity, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1 );
		}
		
		return new ViewModel ( array (
				'receipts' => $receipts,
				'departments' => $departments,
				'department_id' => $department_id,
				
				'balance' => $balance,
				'notified' => $notified,
				'notified_quantity' => $notified_quantity,
				
				'paginator' => $paginator,
				'total_items' => $totalResults 
		) );
	}
	
	/**
	 * Notify action, whn procurement staff received goods, and notify the requester for confirmation.
	 * Ajax
	 */
	public function notifyAction() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$user_id = $user ['id'];
		
		$dn_number = $this->params ()->fromQuery ( 'dn_number' );
		
		// create DN
		$dn = new Delivery ();
		$dn->dn_number = $dn_number;
		$dn->created_by = $user_id;
		
		$dn_id = $this->deliveryTable->add ( $dn );
		
		// update DN Workflow
		$wf = new DeliveryWorkFlow ();
		$wf->delivery_id = $dn_id;
		$wf->status = "Notified";
		$wf->updated_by = $user ['id'];
		$last_workflow_id = $this->deliveryWorkFlowTable->add ( $wf );
		$this->deliveryTable->updateLastWorkFlow ( $dn_id, $last_workflow_id );
		
		// / update delivery items
		$selected_items = $this->params ()->fromQuery ( 'do_items' );
		$this->deliveryItemTable->submitSelectedDOItems ( $selected_items, $dn_id );
		
		// Update or Delete Cart item status
		// $this->deliveryCartTable->setSelectedCartItemsAsNotified ( $selected_items );
		
		// @todo: Update Delivery Item Workflow as notified
		
		$dn_items = $this->deliveryTable->getDeliveryItems ( $dn_id );
		
		if (count ( $dn_items ) > 0) :
			
			foreach ( $dn_items as $dn_item ) {
				$input = new DeliveryItemWorkFlow ();
				$input->delivery_id = $dn_id;
				$input->dn_item_id = $dn_item->dn_item_id;
				$input->pr_item_id = $dn_item->pr_item_id;
				$input->status = "Notified";
				$input->updated_by = $user ['id'];
				$last_workflow_id = $this->deliveryItemWorkFlowTable->add ( $input );
				$this->deliveryItemTable->updateLastWorkFlow ( $dn_item->dn_item_id, $last_workflow_id );
			}
		
		endif;
		
		$this->redirect ()->toUrl ( '/procurement/pr/my-pr' );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editItemAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$input = new DeliveryItem ();
			
			$id = $request->getPost ( 'do_item_id' );
			
			// not update pr_item_id
			$pr_item_id = $request->getPost ( 'pr_item_id' );
			
			$input->name = $request->getPost ( 'name' );
			$input->code = $request->getPost ( 'code' );
			$input->unit = $request->getPost ( 'unit' );
			
			$input->vendor_id = $request->getPost ( 'vendor_id' );
			
			$input->delivered_quantity = $request->getPost ( 'delivered_quantity' );
			$input->price = $request->getPost ( 'price' );
			$input->currency = $request->getPost ( 'curreny' );
			$input->payment_method = $request->getPost ( 'payment_method' );
			
			$input->receipt_date = $request->getPost ( 'receipt_date' );
			$input->invoice_date = $request->getPost ( 'invoice_date' );
			$input->invoice_no = $request->getPost ( 'invoice_no' );
			
			$input->remarks = $request->getPost ( 'remarks' );
			
			// validator.
			$errors = array ();
			
			
			// validator.
			$validator = new Date ();
				
			if (! $validator->isValid ( $input->receipt_date )) {
				$errors [] = 'Receipt date format is not correct!';
			}
				
			if (! $validator->isValid ( $input->invoice_date )) {
				$errors [] = 'Invoice date format is not correct!';
			}
			
			// Fixed it by going to php.ini and uncommenting extension=php_intl.dll
			// $validator = new Int ();
			
			if (! is_numeric ( $input->price )) {
				$errors [] = 'Price is not valid. It must be a number.';
			} else {
				if ($input->price < 0) {
					$errors [] = 'Price must be greater than 0';
				}
			}
			
			if (! is_numeric ( $input->delivered_quantity )) {
				$errors [] = 'Quantity is not valid. It must be a number.';
			} else {
				if ($input->delivered_quantity < 0) {
					$errors [] = 'Quantity must be greate than 0';
				}
			}
			
			if ($input->currency =="") {
				$errors [] = 'Please select currency!';
			}
				
			if ($input->payment_method =="") {
				$errors [] = 'Please select payment method!';
			}
			
			if (count ( $errors ) > 0) {
				$do_item = $this->deliveryItemTable->getDOItem ( $id);
				$pr_item = $this->purchaseRequestItemTable->getPRItem ( $pr_item_id );
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'errors' => $errors,
						'pr_item' => $pr_item,
						'do_item' => $do_item,
						'submitted_do_item' => $input,
						'vendor_name' => $request->getPost ( 'vendor' ),
				)
				 );
			}
			
			$this->deliveryItemTable->updateGR ( $input, $id );
			$this->redirect ()->toUrl ( $redirectUrl );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$pr_item_id = ( int ) $this->params ()->fromQuery ( 'pr_item_id' );
		$pr_item = $this->purchaseRequestItemTable->getPRItem ( $pr_item_id );
		
		$do_item_id = ( int ) $this->params ()->fromQuery ( 'do_item_id' );
		$do_item = $this->deliveryItemTable->getDOItem ( $do_item_id );
		
		return new ViewModel ( array (
				'do_item' => $do_item,
				'pr_item' => $pr_item,
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'submitted_do_item' => null,
				'vendor_name' => $do_item->vendor_name,
		) );
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
	public function getDeliveryItemWorkFlowTable() {
		return $this->deliveryItemWorkFlowTable;
	}
	public function setDeliveryItemWorkFlowTable(DeliveryItemWorkFlowTable $deliveryItemWorkFlowTable) {
		$this->deliveryItemWorkFlowTable = $deliveryItemWorkFlowTable;
		return $this;
	}
	public function getArticleMovementTable() {
		return $this->articleMovementTable;
	}
	public function setArticleMovementTable(ArticleMovementTable $articleMovementTable) {
		$this->articleMovementTable = $articleMovementTable;
		return $this;
	}
	public function getArticleLastDNTable() {
		return $this->articleLastDNTable;
	}
	public function setArticleLastDNTable(ArticleLastDNTable $articleLastDNTable) {
		$this->articleLastDNTable = $articleLastDNTable;
		return $this;
	}
	public function getSparepartMovementTable() {
		return $this->sparepartMovementTable;
	}
	public function setSparepartMovementTable($sparepartMovementTable) {
		$this->sparepartMovementTable = $sparepartMovementTable;
		return $this;
	}
	public function getSparepartLastDNTable() {
		return $this->sparepartLastDNTable;
	}
	public function setSparepartLastDNTable($sparepartLastDNTable) {
		$this->sparepartLastDNTable = $sparepartLastDNTable;
		return $this;
	}
	public function getDeliveryCartTable() {
		return $this->deliveryCartTable;
	}
	public function setDeliveryCartTable(DeliveryCartTable $deliveryCartTable) {
		$this->deliveryCartTable = $deliveryCartTable;
		return $this;
	}
	public function getPoItemTable() {
		return $this->poItemTable;
	}
	public function setPoItemTable(POItemTable $poItemTable) {
		$this->poItemTable = $poItemTable;
		return $this;
	}
}
