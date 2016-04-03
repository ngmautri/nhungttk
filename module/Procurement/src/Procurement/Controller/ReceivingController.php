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

use Procurement\Model\PurchaseRequest;
use Procurement\Model\PurchaseRequestTable;

use Procurement\Model\PurchaseRequestItem;
use Procurement\Model\PurchaseRequestItemTable;

use Procurement\Model\PurchaseRequestItemPic;
use Procurement\Model\PurchaseRequestItemPicTable;


class PRController extends AbstractActionController {
	
	protected  $purchaseRequetTable;
	protected  $purchaseRequetItemTable;
	protected  $purchaseRequetItemPicTable;
	
	protected  $authService;
	
	
	
	public function indexAction() {
		return new ViewModel ();
	}
	
	public function createAction() {
		return new ViewModel ();
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
	
	
	public function getPurchaseRequetTable() {
		return $this->purchaseRequetTable;
	}
	public function setPurchaseRequetTable(PurchaseRequestTable $purchaseRequetTable) {
		$this->purchaseRequetTable = $purchaseRequetTable;
		return $this;
	}
	public function getPurchaseRequetItemTable() {
		return $this->purchaseRequetItemTable;
	}
	public function setPurchaseRequetItemTable(PurchaseRequestItemTable $purchaseRequetItemTable) {
		$this->purchaseRequetItemTable = $purchaseRequetItemTable;
		return $this;
	}
	public function getPurchaseRequetItemPicTable() {
		return $this->purchaseRequetItemPicTable;
	}
	public function setPurchaseRequetItemPicTable(PurchaseRequestItemPicTable $purchaseRequetItemPicTable) {
		$this->purchaseRequetItemPicTable = $purchaseRequetItemPicTable;
		return $this;
	}
	public function getAuthService() {
		return $this->authService;
	}
	public function setAuthService($authService) {
		$this->authService = $authService;
		return $this;
	}
	
	
	
}
