<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace User\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use User\Model\User;
use MLA\Files;


class AdminController extends AbstractActionController {
	protected $userTable;
	protected $authService;
	protected $registerService;
	protected $assetSearchService;
	protected $sparePartSearchService;
	protected $articleSearchService;
	protected $vendorSearchService;
	
	protected $mesage;
	
	
	public $massage = 'NULL';
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		return new ViewModel ( array (
				'users' => $this->getUserTable ()->fetchAll (),
		) );
	}
	
	public function assetIndexAction() {
		$searcher = $this->getAssetSearchService();
		$message = $searcher->createIndex();
		
		return new ViewModel ( array (
				'message' => $message,
			) );
		
	}
	
	
	public function sparePartIndexAction() {
		
		$searcher = $this->getSparePartSearchService();
		$message = $searcher->createIndex();
		
		return new ViewModel ( array (
				'message' => $message,
		) );
	
	}
	
	public function vendorIndexAction() {
	
		$searcher = $this->getVendorSearchService();
		$message = $searcher->createIndex();
	
		return new ViewModel ( array (
				'message' => $message,
		) );
	
	}
	
	public function articleIndexAction() {
	
		$searcher = $this->getArticleSearchService();
		$message = $searcher->createIndex();
	
		return new ViewModel ( array (
				'message' => $message,
		) );
	
	}
	
	
		
	// get UserTable
	public function getUserTable() {
		if (! $this->userTable) {
			$sm = $this->getServiceLocator ();
			$this->userTable = $sm->get ( 'User\Model\UserTable' );
		}
		return $this->userTable;
	}
	
	/*
	 * Get Authentication Service
	 */
	public function getAuthService() {
		if (! $this->authService) {
			$this->authservice = $this->getServiceLocator ()->get ( 'AuthService' );
		}
		return $this->authservice;
	}
	
	
	/**
	 * Get asset search service
	 */
	public function getAssetSearchService() {
		if (! $this->assetSearchService) {
			$this->assetSearchService = $this->getServiceLocator ()->get ( 'Inventory\Services\AssetSearchService' );
		}
		return $this->assetSearchService;
	}
	
	/**
	 * Get asset search service
	 */
	public function getSparePartSearchService() {
		if (! $this->sparePartSearchService) {
			$this->sparePartSearchService = $this->getServiceLocator ()->get ( 'Inventory\Services\SparePartsSearchService' );
		}
		return $this->sparePartSearchService;
	}
	
	/**
	 * Get asset search service
	 */
	public function getArticleSearchService() {
		if (! $this->articleSearchService) {
			$this->articleSearchService = $this->getServiceLocator ()->get ( 'Inventory\Services\ArticleSearchService' );
		}
		return $this->articleSearchService;
	}
	
	/**
	 * Get asset search service
	 */
	public function getVendorSearchService() {
		if (! $this->vendorSearchService) {
			$this->vendorSearchService = $this->getServiceLocator ()->get ( 'Procurement\Services\VendorSearchService' );
		}
		return $this->vendorSearchService;
	}
	
}
