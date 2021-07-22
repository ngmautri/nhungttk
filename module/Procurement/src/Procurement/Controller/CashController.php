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
class CashController extends AbstractActionController {
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
	
	public function requestAction() {
		return new ViewModel ();
	}
	
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addToCartAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
	
		if ($request->isPost ()) {
				
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$input = new POItem();
				
			$input->pr_item_id = $request->getPost ( 'pr_item_id' );
				
			$input->name = $request->getPost ( 'name' );
			$input->code = $request->getPost ( 'unit' );
			$input->unit = $request->getPost ( 'unit' );
			
			$input->vendor_id = $request->getPost ( 'vendor_id' );
			
			$input->price = $request->getPost ( 'price' );
			$input->currency = $request->getPost ( 'curreny' );
			$input->payment_method = $request->getPost ( 'payment_method' );
				
			$input->remarks = $request->getPost ( 'remarks' );
			$input->created_by = $user ['id'];
			$input->status = "SAVED";
				
			$pr_item = $this->purchaseRequestItemTable->getPRItem ( $input->pr_item_id );
				
			// validator.
			$errors = array ();
				
			// Fixed it by going to php.ini and uncommenting extension=php_intl.dll
			$validator = new Int ();
				
			if (! is_numeric ( $input->price )) {
				$errors [] = 'Price is not valid. It must be a number.';
			}
				
			if (count ( $errors ) > 0) {
				$pr_item = $this->purchaseRequestItemTable->getPRItem ( $input->pr_item_id );
	
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'user' => $user,
						'errors' => $errors,
						'pr_item' => $pr_item
				) );
			}
				
			$this->poItemTable->add ( $input );
			$this->redirect ()->toUrl ( $redirectUrl );
		}
	
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
	
		$pr_item_id = ( int ) $this->params ()->fromQuery ( 'pr_item_id' );
		$pr_item = $this->purchaseRequestItemTable->getPRItem ( $pr_item_id );
	
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'pr_item' => $pr_item
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
		
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
	
		$department_id = $this->params ()->fromQuery ( 'department_id' );
		$balance = $this->params ()->fromQuery ( 'balance' );
		
		$payment_method= $this->params ()->fromQuery ( 'payment_method' );
		$currency = $this->params ()->fromQuery ( 'currency' );
		
		$departments = $this->departmentTable->fetchAll ();
	
		$vendor_id = $this->params ()->fromQuery ( 'vendor_id' );
		$vendors = $this->poItemTable->getVendorsOfPOList();
	
		if ($department_id == null) :
			$department_id = 0;
		endif;
	
		if ($department_id == null) :
			$department_id = 0;
		endif;
		
		if ($balance == null) :
			$balance = 1;
		endif;
	
		$po_items = $this->poItemTable->getPOItems($balance, $department_id,$vendor_id,$payment_method, $currency,0, 0 );
		$totalResults = count ( $po_items );
	
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$po_items = $this->poItemTable->getPOItems ($balance,$department_id,$vendor_id, $payment_method, $currency,($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1 );
		}
	
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'po_items' => $po_items,
				'departments' => $departments,
				'balance' => $balance,
				'department_id' => $department_id,
				'payment_method' => $payment_method,
				'currency' => $currency,
				'vendors' => $vendors,
				'vendor_id' => $vendor_id,
				'paginator' => $paginator,
				'total_items' => $totalResults
		) );
	}
	
	
	
	
	
	//++++++++++++++++++++++++
	
	
	
	public function getNotificationAction() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$notified_dn_items = $this->deliveryItemTable->getNotifiedDNItemsOf ( $user ['id'] );
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'dn_items' => $notified_dn_items 
		) );
	}
	
	/**
	 * Ajax
	 * ================================
	 */
	public function confirmAction() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$dn_id = $this->params ()->fromQuery ( 'dn_id' );
		$dn_item_id = $this->params ()->fromQuery ( 'dn_item_id' );
		$pr_item_id = $this->params ()->fromQuery ( 'pr_item_id' );
		$sparepart_id = $this->params ()->fromQuery ( 'sparepart_id' );
		$article_id = ( int ) $this->params ()->fromQuery ( 'article_id' );
		$asset_id = $this->params ()->fromQuery ( 'asset_id' );
		
		$dn_item = $this->deliveryItemTable->get ( $dn_item_id );
		
		$input = new DeliveryItemWorkFlow ();
		$input->delivery_id = $dn_id;
		$input->dn_item_id = $dn_item_id;
		$input->pr_item_id = $pr_item_id;
		$input->status = "Confirmed";
		$input->updated_by = $user ['id'];
		$last_workflow_id = $this->deliveryItemWorkFlowTable->add ( $input );
		$this->deliveryItemTable->updateLastWorkFlow ( $dn_item_id, $last_workflow_id );
		
		if ($article_id > 0) :
			
			// up-date-movement
			$m = new ArticleMovement ();
			$m->movement_date = $dn_item->created_on;
			$m->article_id = $article_id;
			
			$m->flow = "IN";
			$m->quantity = $dn_item->delivered_quantity;
			$m->pr_item_id = $pr_item_id;
			$m->dn_item_id = $dn_item_id;
			$m->created_by = $user ['id'];
			$m->comment = 'PR:' . $dn_item->pr_number;
			
			$this->articleMovementTable->add ( $m );
			
			$i = new ArticleLastDN ();
			$i->article_id = $article_id;
			$i->last_workflow_id = $last_workflow_id;
			$this->articleLastDNTable->add ( $i );
		
		endif;
		
		if ($sparepart_id > 0) :
			$input = new SparepartMovement ();
			$input->movement_date = $dn_item->created_on;
			$input->sparepart_id = $sparepart_id;
			$input->quantity = $dn_item->delivered_quantity;
			$input->flow = 'IN';
			$input->comment = 'PR:' . $dn_item->pr_number;
			$input->created_by = $user ['id'];
			
			$this->sparepartMovementTable->add ( $input );
			
			$i = new SparepartLastDN ();
			$i->sparepart_id = $sparepart_id;
			$i->last_workflow_id = $last_workflow_id;
			$this->sparepartLastDNTable->add ( $i );
		
		endif;
		
		$this->redirect ()->toUrl ( '/procurement/delivery/get-notification' );
	}
	
	/**
	 * Ajax
	 * ================================
	 */
	public function confirmDeliveryAction() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$dn_id = $this->params ()->fromQuery ( 'dn_id' );
		$dn_item_id = $this->params ()->fromQuery ( 'dn_item_id' );
		$pr_item_id = $this->params ()->fromQuery ( 'pr_item_id' );
		$sparepart_id = $this->params ()->fromQuery ( 'sparepart_id' );
		$article_id = ( int ) $this->params ()->fromQuery ( 'article_id' );
		$asset_id = $this->params ()->fromQuery ( 'asset_id' );
		$confirmed_quantity = ( int ) $this->params ()->fromQuery ( 'confirmed_quantity' );
		$rejected_quantity = ( int ) $this->params ()->fromQuery ( 'rejected_quantity' );
		
		$dn_item = $this->deliveryItemTable->get ( $dn_item_id );
		
		$input = new DeliveryItemWorkFlow ();
		$input->delivery_id = $dn_id;
		$input->dn_item_id = $dn_item_id;
		$input->pr_item_id = $pr_item_id;
		$input->status = "Confirmed";
		$input->updated_by = $user ['id'];
		$input->confirmed_quantity = $confirmed_quantity;
		$input->rejected_quantity = $rejected_quantity;
		$input->remarks = $this->params ()->fromQuery ( 'remarks' );
		;
		
		$last_workflow_id = $this->deliveryItemWorkFlowTable->add ( $input );
		
		$this->deliveryItemTable->updateLastWorkFlow ( $dn_item_id, $last_workflow_id );
		
		if ($article_id > 0) :
			
			// up-date-movement
			$m = new ArticleMovement ();
			$m->movement_date = $dn_item->created_on;
			$m->article_id = $article_id;
			
			$m->flow = "IN";
			$m->quantity = $dn_item->delivered_quantity;
			$m->pr_item_id = $pr_item_id;
			$m->dn_item_id = $dn_item_id;
			$m->created_by = $user ['id'];
			$m->comment = 'PR:' . $dn_item->pr_number;
			
			$this->articleMovementTable->add ( $m );
			
			$i = new ArticleLastDN ();
			$i->article_id = $article_id;
			$i->last_workflow_id = $last_workflow_id;
			$this->articleLastDNTable->add ( $i );
		
		endif;
		
		if ($sparepart_id > 0) :
			$input = new SparepartMovement ();
			$input->movement_date = $dn_item->created_on;
			$input->sparepart_id = $sparepart_id;
			$input->quantity = $dn_item->delivered_quantity;
			$input->flow = 'IN';
			$input->comment = 'PR:' . $dn_item->pr_number;
			$input->created_by = $user ['id'];
			
			$this->sparepartMovementTable->add ( $input );
			
			$i = new SparepartLastDN ();
			$i->sparepart_id = $sparepart_id;
			$i->last_workflow_id = $last_workflow_id;
			$this->sparepartLastDNTable->add ( $i );
		
		endif;
		$this->redirect ()->toUrl ( '/procurement/delivery/get-notification' );
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
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createStep2Action() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$dn_id = ( int ) $this->params ()->fromQuery ( 'dn_id' );
		$dn = $this->deliveryTable->getDeliveries ( $dn_id, 0, 0, 0 )->current ();
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
			$wf = new DeliveryWorkFlow ();
			$wf->delivery_id = $dn_id;
			$wf->status = "Notified";
			$wf->updated_by = $user ['id'];
			$last_workflow_id = $this->deliveryWorkFlowTable->add ( $wf );
			$this->deliveryTable->updateLastWorkFlow ( $dn_id, $last_workflow_id );
			
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
		
		$this->redirect ()->toUrl ( '/procurement/pr/all-pr' );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function itemToDeliverAction() {
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
		$user_id = $this->params ()->fromQuery ( 'user_id' );
		$department_id = $this->params ()->fromQuery ( 'department_id' );
		$departments = $this->departmentTable->fetchAll ();
		
		if ($department_id == null) :
			$department_id = '';
		
		
		
		endif;
		
		$pr_items = $this->purchaseRequestItemTable->getPRItemsToDeliver ( $department_id, 0, 0 );
		$totalResults = count ( $pr_items );
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$pr_items = $this->purchaseRequestItemTable->getPRItemsToDeliver ( $department_id, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1 );
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
				'total_items' => $totalResults 
		) );
	}
	

	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function reviewCartAction() {
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 50;
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
		
		$department_id = $this->params ()->fromQuery ( 'department_id' );
		$departments = $this->departmentTable->fetchAll ();
		
		$vendor_id = $this->params ()->fromQuery ( 'vendor_id' );
		$vendors = $this->deliveryCartTable->getVendorsInDeliveryList();
		
		if ($department_id == null) :
			$department_id = 0;
		endif;
		
		if ($department_id == null) :
		$department_id = 0;
		endif;
		
		$cart_items = $this->deliveryCartTable->getDNCartItems ( $department_id,$vendor_id,0, 0 );
		$totalResults = count ( $cart_items );
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$cart_items = $this->deliveryCartTable->getDNCartItems ($department_id,$vendor_id, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1 );
		}
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'cart_items' => $cart_items,
				'departments' => $departments,
				'department_id' => $department_id,
				'vendors' => $vendors,
				'vendor_id' => $vendor_id,
				'paginator' => $paginator,
				'total_items' => $totalResults 
		) );
	}
	
	/**
	 * Submit Delivery NOTE
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function submitCartItemsAction() {
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$user_id = $user ['id'];
		
		$dn_number = $this->params ()->fromQuery ( 'dn_number' );
		$select_all_item = $this->params ()->fromQuery ( 'SelectAll' );
		
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
		
		// Moving Cart Item into Delivery item table
		$selected_items = $this->params ()->fromQuery ( 'cart_items' );
		$this->deliveryCartTable->submitSelectedCartItems ( $selected_items, $dn_id );
		
		// Update or Delete Cart item status
		$this->deliveryCartTable->setSelectedCartItemsAsNotified ( $selected_items );
		
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
	 * AJAX
	 */
	public function deleteCartItemAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$this->deliveryCartTable->delete ( $id );
		
		$c = array (
				'status' => $id . ' deleted' 
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
			$pr_items = $this->purchaseRequestItemTable->getAllSubmittedPRItems ( $last_status, $user_id, $department_id, 1, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1 );
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
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$input = new DeliveryItem ();
			
			$input->delivery_date = $request->getPost ( 'delivery_date' );
			$input->delivery_id = $request->getPost ( 'delivery_id' );
			$input->pr_item_id = $request->getPost ( 'pr_item_id' );
			
			$input->name = $request->getPost ( 'name' );
			$input->code = $request->getPost ( 'unit' );
			$input->unit = $request->getPost ( 'unit' );
			
			$input->vendor_id = $request->getPost ( 'vendor_id' );
			
			$input->delivered_quantity = $request->getPost ( 'delivered_quantity' );
			$input->price = $request->getPost ( 'price' );
			$input->currency = $request->getPost ( 'curreny' );
			$input->payment_method = $request->getPost ( 'payment_method' );
			
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
				
				/*
				 * OK to delivery more
				 * if ($input->delivered_quantity > $to_delivery) {
				 * $errors [] = 'Deliver quantity is: ' . $input->delivered_quantity . ' pcs, which is bigger than amount to delivery';
				 * }
				 */
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
				) );
			}
			
			$this->deliveryItemTable->add ( $input );
			$this->redirect ()->toUrl ( $redirectUrl );
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
		$my_deliveries = $this->deliveryTable->getDeliveries ( 0, $user ['id'], 0, 0 );
		
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
