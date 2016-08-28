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
use Zend\Http\Headers;
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
class POController extends AbstractActionController {
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
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addToCartAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$input = new POItem ();
			
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
		
		// $request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$department_id = $this->params ()->fromQuery ( 'department_id' );
		$balance = $this->params ()->fromQuery ( 'balance' );
		
		$payment_method = $this->params ()->fromQuery ( 'payment_method' );
		$currency = $this->params ()->fromQuery ( 'currency' );
		
		$departments = $this->departmentTable->fetchAll ();
		
		$vendor_id = $this->params ()->fromQuery ( 'vendor_id' );
		$vendors = $this->poItemTable->getVendorsOfPOList ();
		
		if ($department_id == null) :
			$department_id = 0;
		
		endif;
		
		if ($department_id == null) :
			$department_id = 0;
		
		endif;
		
		if ($balance == null) :
			$balance = 1;
		
		endif;
		
		$output = $this->params ()->fromQuery ( 'output' );
		
		$po_items = $this->poItemTable->getPOItems ( $balance, $department_id, $vendor_id, $payment_method, $currency, 0, 0 );
		$totalResults = count ( $po_items );
		
		
		if ($output === 'csv') {				
			$fh = fopen ( 'php://memory', 'w' );
			// $myfile = fopen('ouptut.csv', 'a+');
				
			$h = array ();
			$h [] = "Item ID";
			$h [] = "Item Name";
			$h [] = "Item Code";
			$h [] = "Item Unit";
			$h [] = "Item Keywords";
			$h [] = "PR number";
			$h [] = "PR auto number";
			$h [] = "Requester";
			$h [] = "Department";
			
			
			$h [] = "Ordered Quantity";
			$h [] = "Received Quantity";
			$h [] = "Notified Quantity";
			$h [] = "Confirmed Quantity";
			$h [] = "Rejected Quantity";
			$h [] = "Balance";
			$h [] = "Free Quantity";

			$h [] = "Vendor";
			$h [] = "Unit Price";
			$h [] = "Currency";
			$h [] = "Payment Method";
			
			$h [] = "Remarks";
				
			$delimiter = ";";
				
			fputcsv ( $fh, $h, $delimiter, '"' );
			// fputs($fh, implode($h, ',')."\n");
				
				
			foreach ( $po_items as $m ) {
				$l = array ();
				$l [] = ( string ) $m->pr_item_id;
				$l [] = ( string ) $m->pr_item_name;
				$l [] = ( string ) $m->pr_item_code;
				$l [] = ( string ) $m->pr_item_unit;
				$l [] = ( string ) $m->pr_item_keywords;
				$l [] = ( string ) $m->pr_number;
				$l [] = ( string ) $m->auto_pr_number;
				$l [] = ( string ) $m->pr_requester_name;
				$l [] = ( string ) $m->pr_of_department;
				
				
				$l [] = ( string ) $m->ordered_quantity;
				$l [] = ( string ) $m->total_received_quantity;
				$l [] = ( string ) $m->unconfirmed_quantity;
				$l [] = ( string ) $m->confirmed_quantity;
				$l [] = ( string ) $m->rejected_quantity;
				$l [] = ( string ) $m->confirmed_balance;
				$l [] = ( string ) $m->confirmed_free_balance;
				
				$l [] = ( string ) $m->vendor_name;
				$l [] = ( string ) $m->price;
				$l [] = ( string ) $m->currency;
				$l [] = ( string ) $m->payment_method;
				
				$l [] = ( string ) $m->remarks;
		
				fputcsv ( $fh, $l, $delimiter, '"' );
				// fputs($fh, implode($l, ',')."\n");
			}
				
			$fileName = 'po-items-'.date( "m-d-Y" ) .'-' . date("h:i:sa").'.csv';
			fseek ( $fh, 0 );
			$output = stream_get_contents ( $fh );
			// file_put_contents($fileName, $output);
				
			$response = $this->getResponse ();
			$headers = new Headers();
				
			$headers->addHeaderLine ( 'Content-Type: text/csv' );
			//$headers->addHeaderLine ( 'Content-Type: application/vnd.ms-excel; charset=UTF-8' );
				
			$headers->addHeaderLine ( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
			$headers->addHeaderLine ( 'Content-Description: File Transfer' );
			$headers->addHeaderLine ( 'Content-Transfer-Encoding: binary' );
			$headers->addHeaderLine ( 'Content-Encoding: UTF-8' );
				
			$response->setHeaders($headers);
			// $output = fread($fh, 8192);
				
			$response->setContent ( $output );
				
			fclose ( $fh );
			// unlink($fileName);
			return $response;
		}
		
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
	
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$po_items = $this->poItemTable->getPOItems ($balance,$department_id,$vendor_id, $payment_method, $currency,($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
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
	
	/**
	 * Edit PO Item
	 * @return \Zend\View\Model\ViewModel
	 */
	
	public function editItemAction() {
		
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
				
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$input = new POItem ();
				
			$id = $request->getPost ( 'id' );
			$input->pr_item_id = $request->getPost ( 'pr_item_id' );
				
			$input->name = $request->getPost ( 'name' );
			$input->code = $request->getPost ( 'unit' );
				
			$input->vendor_id = $request->getPost ( 'vendor_id' );
				
			$input->price = $request->getPost ( 'price' );
			$input->currency = $request->getPost ( 'curreny' );
			$input->payment_method = $request->getPost ( 'payment_method' );
			$input->remarks = $request->getPost ( 'remarks' );
			
			$input->created_by = $user ['id'];
			$input->status = "SAVED";
			
				
				
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
						'errors' => $errors,
						'pr_item' => $pr_item,
						'po_item' => $input,
						
				) );
			}
				
			$this->poItemTable->update ( $input,$id);
			$this->redirect ()->toUrl ( $redirectUrl );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$po_item = $this->poItemTable->getPOItem($id );
		
		$pr_item_id = ( int ) $this->params ()->fromQuery ( 'pr_item_id' );
		$pr_item = $this->purchaseRequestItemTable->getPRItem ( $pr_item_id );
		
		return new ViewModel ( array (
				'po_item' => $po_item,
				'pr_item' => $pr_item,
				'redirectUrl' => $redirectUrl,
				'errors' => null
		) 
		);
	}

	//+++++++++++++++++++++++++++++++++++
	
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
