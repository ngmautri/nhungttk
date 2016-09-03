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
class DOController extends AbstractActionController {
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
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$deliveries = $this->deliveryTable->getDOList ( 0, 0 );
		$totalResults = count ( $deliveries );
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$deliveries = $this->deliveryTable->getDOList ( ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'deliveries' => $deliveries,
				'paginator' => $paginator,
				'total_items' => $totalResults 
		)
		 );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function showAction() {
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
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function getNotificationAction() {
		
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$pr_item_id = $this->params ()->fromQuery ( 'pr_item_id' );
		
		$notified_dn_items = $this->deliveryItemTable->getNotifiedDNItemsOf ( $user ['id'], $pr_item_id );
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'user' => $user,
				'errors' => null,
				'dn_items' => $notified_dn_items 
		) );
	}
	
	/**
	 * AJAXT
	 * Confirm DO by requester.
	 */
	public function confirmDOAction() {
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
		
		
		// @TODO: Check before update movement. 
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
			$m->comment = 'Good Receipt with DO-Items#:' . $dn_item_id;
			
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
			$input->comment = 'Good Receipt with DO-Items#:' . $dn_item_id;
			$input->created_by = $user ['id'];
			
			$this->sparepartMovementTable->add ( $input );
			
			$i = new SparepartLastDN ();
			$i->sparepart_id = $sparepart_id;
			$i->last_workflow_id = $last_workflow_id;
			$this->sparepartLastDNTable->add ( $i );
		
		endif;
		$this->redirect ()->toUrl ( '/procurement/do/get-notification' );
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
