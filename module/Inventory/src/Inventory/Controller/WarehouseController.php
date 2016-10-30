<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory\Controller;

use Zend\I18n\Validator\Int;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;
use Zend\Http\Headers;
use MLA\Paginator;
use MLA\Files;
use Inventory\Model\MLASparepart;
use Inventory\Model\MLASparepartTable;
use Inventory\Model\Article;
use Inventory\Model\ArticleTable;
use Inventory\Services\ArticleService;
use Inventory\Services\ArticleSearchService;
use Inventory\Model\ArticlePicture;
use Inventory\Model\ArticlePictureTable;
use Inventory\Model\ArticleCategory;
use Inventory\Model\ArticleCategoryTable;
use Inventory\Model\ArticleCategoryMember;
use Inventory\Model\ArticleCategoryMemberTable;
use Inventory\Model\ArticlePurchasing;
use Inventory\Model\ArticlePurchasingTable;
use Application\Model\DepartmentTable;
use Inventory\Model\SparepartPurchasing;
use Inventory\Model\SparepartPurchasingTable;
use Inventory\Model\Warehouse;
use Inventory\Model\WarehouseTable;

class WarehouseController extends AbstractActionController {
	protected $SmtpTransportService;
	protected $authService;
	protected $userTable;
	protected $articleTable;
	protected $articlePurchasingTable;
	protected $spPurchasingTable;
	protected $sparePartTable;
	protected $departmentTable;
	protected $whTable;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 * Add new purchase data
	 */
	public function listAction() {
		$request = $this->getRequest ();
		if ($request->isXmlHttpRequest ()) {
			$this->layout ( "layout/inventory/ajax" );
		}
		
		$warehouses = $this->whTable->fetchAll ();
		return new ViewModel ( array (
				'warehouses' => $warehouses 
		) );
	}
	
	/**
	 * Add new purchase data
	 */
	public function selectListAction() {
		$request = $this->getRequest ();
		$wh= $this->params ()->fromQuery ( 'wh' );
		
		
		if ($request->isXmlHttpRequest ()) {
			$this->layout ( "layout/inventory/ajax" );
		}
	
		$warehouses = $this->whTable->fetchAll ();
		return new ViewModel ( array (
				'warehouses' => $warehouses,
				'wh'=>$wh
		) );
	}
	
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			if ($request->isPost ()) {
				
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$input = new Warehouse();
				$input->wh_code = $request->getPost ( 'wh_code' );
				$input->wh_name = $request->getPost ( 'wh_name' );
				$input->wh_address = $request->getPost ( 'wh_address' );
				$input->wh_country = $request->getPost ( 'wh_country' );
				$input->wh_contract_person = $request->getPost ( 'wh_contract_person' );
				$input->wh_email = $request->getPost ( 'wh_email' );
				$input->wh_status = $request->getPost ( 'wh_status' );
					
				$errors = array ();
				
					
				if ($input->wh_code == "") {
					$errors [] = 'Please give warehouse code!';
				}else{
					if($this->whTable->isWHCodeExits($input->wh_code)){
						$errors [] = 'Warehouse code ' . $input->wh_code .' exits already';
					}
				}
				
				if ($input->wh_name == "") {
					$errors [] = 'Please give warehouse name!';
				}
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'submitted_data' => $input,
							'errors' =>$errors
					) );
				}
				
				$this->whTable->add($input);			
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
			
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'submitted_data' => null,
				'errors'=>null
		) );
	}
	public function getSmtpTransportService() {
		return $this->SmtpTransportService;
	}
	public function setSmtpTransportService($SmtpTransportService) {
		$this->SmtpTransportService = $SmtpTransportService;
		return $this;
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
	public function getArticleTable() {
		return $this->articleTable;
	}
	public function setArticleTable(ArticleTable $articleTable) {
		$this->articleTable = $articleTable;
		return $this;
	}
	public function getSparePartTable() {
		return $this->sparePartTable;
	}
	public function setSparePartTable(MLASparepartTable $sparePartTable) {
		$this->sparePartTable = $sparePartTable;
		return $this;
	}
	public function getDepartmentTable() {
		return $this->departmentTable;
	}
	public function setDepartmentTable($departmentTable) {
		$this->departmentTable = $departmentTable;
		return $this;
	}
	public function getArticlePurchasingTable() {
		return $this->articlePurchasingTable;
	}
	public function setArticlePurchasingTable(ArticlePurchasingTable $articlePurchasingTable) {
		$this->articlePurchasingTable = $articlePurchasingTable;
		return $this;
	}
	public function getSpPurchasingTable() {
		return $this->spPurchasingTable;
	}
	public function setSpPurchasingTable(SparepartPurchasingTable $spPurchasingTable) {
		$this->spPurchasingTable = $spPurchasingTable;
		return $this;
	}
	public function getWhTable() {
		return $this->whTable;
	}
	public function setWhTable(WarehouseTable $whTable) {
		$this->whTable = $whTable;
		return $this;
	}
	
	
	// SETTER AND GETTER
}
