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


use Procurement\Model\Vendor;
use Procurement\Model\VendorTable;

use User\Model\UserTable;

/**
 * @author nmt
 *
 */
class VendorController extends AbstractActionController {
	protected  $userTable;
	
	protected  $purchaseRequestItemTable;
	protected  $purchaseRequestTable;
	
	
	protected $deliveryTable;
	protected $deliveryItemTable;
	
	protected $vendorTable;
	
	
	protected  $authService;
	
	
	
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
		
				$input = new Delivery();
				$input->dn_number = $request->getPost ( 'dn_number' );
				$input->description = $request->getPost ( 'description' );				
				$input->created_by = $user['id'];
				
				// validator.
				$errors = array();
		
				if ($input->dn_number ==''){
					$errors [] = 'Please give a deliver number';
				}
					
		
				if (count ( $errors ) > 0) {
						return new ViewModel ( array (
							'redirectUrl'=>$redirectUrl,
							'user' =>$user,
							'errors' => $errors,
					) );
				}
		
				$newId = $this->deliveryTable->add($input);
				$this->redirect()->toUrl('/procurement/delivery/create-step2?dn_id='.$newId);
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
		$dn_id = (int) $this->params ()->fromQuery ( 'dn_id' );
		$dn = $this->deliveryTable->get($dn_id);
		$dn_items = $this->deliveryItemTable->getItemsByDN($dn_id);
	
		return new ViewModel ( array (
				'redirectUrl'=>$redirectUrl,
				'user' =>$user,
				'errors' => null,
				'dn'=>$dn,
				'dn_items'=>$dn_items,
		));
	}
	
	/*
	 * 
	 */
	
	public function selectFromList1Action() {
	
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity();
		$user=$this->userTable->getUserByEmail($identity);
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		$dn_id = (int) $this->params ()->fromQuery ( 'dn_id' );
		$dn = $this->deliveryTable->get($dn_id);
		$pr_items = $this->purchaseRequestItemTable->getItems();
	
		return new ViewModel ( array (
				'redirectUrl'=>$redirectUrl,
				'user' =>$user,
				'errors' => null,
				'dn'=>$dn,
				'pr_items'=>$pr_items,
		));
	}
	
	/*
	 * 
	 */
	public function selectFromList2Action() {
	
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity();
		$user=$this->userTable->getUserByEmail($identity);
		if ($request->isPost ()) {
		
			if ($request->isPost ()) {
				$redirectUrl  = $request->getPost ( 'redirectUrl' );
		
				$input = new DeliveryItem();
				$input->delivery_id= $request->getPost ( 'delivery_id' );
				$input->pr_item_id= $request->getPost ( 'pr_item_id' );
				
				
				$input->delivered_quantity= $request->getPost ( 'delivered_quantity' );
				$input->price= $request->getPost ( 'price' );
				$input->currency= $request->getPost ( 'curreny' );
				$input->notes= $request->getPost ( 'notes' );
				
				$pr_item = $this->purchaseRequestItemTable->getItem($input->pr_item_id);
				$to_delivery = $pr_item->quantity - $pr_item->delivered_quantity;
				
				
				// validator.
				$errors = array();
		
				if ($input->delivery_id ==''){
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
				
				if(!is_numeric($input->price)){
					$errors [] = 'Price is not valid. It must be a number.';
				}
				
				
		
				if (count ( $errors ) > 0) {
					$dn = $this->deliveryTable->get($input->delivery_id);
					$pr_item = $this->purchaseRequestItemTable->getItem($input->pr_item_id);
					
						
					return new ViewModel ( array (
							'redirectUrl'=>$redirectUrl,
							'user' =>$user,
							'errors' => $errors,
							'dn'=>$dn,
							'pr_item'=>$pr_item,
								
					) );
				}
		
				$newId = $this->deliveryItemTable->add($input);
				$this->redirect()->toUrl($redirectUrl);
			}
		}
		
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		$dn_id = (int) $this->params ()->fromQuery ( 'dn_id' );
		$dn = $this->deliveryTable->get($dn_id);
		
		$pr_item_id = (int) $this->params ()->fromQuery ( 'pr_item_id' );
		$pr_item = $this->purchaseRequestItemTable->getItem($pr_item_id);
		
		
	
		return new ViewModel ( array (
				'redirectUrl'=>$redirectUrl,
				'user' =>$user,
				'errors' => null,
				'dn'=>$dn,
				'pr_item'=>$pr_item,
		));
	}
	
	public function myDeliveryAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity();
		$user=$this->userTable->getUserByEmail($identity);
	
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		$my_deliveries=$this->deliveryTable->getDeliveryOf($user['id']);
	
	
		return new ViewModel ( array (
				'redirectUrl'=>$redirectUrl,
				'user' =>$user,
				'errors' => null,
				'my_delivery'=>$my_deliveries,
		));
	
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
	public function getVendorTable() {
		return $this->vendorTable;
	}
	public function setVendorTable(VendorTable $vendorTable) {
		$this->vendorTable = $vendorTable;
		return $this;
	}
	
	
	
	
}
