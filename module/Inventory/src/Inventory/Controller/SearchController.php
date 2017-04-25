<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\MultiTerm;
use ZendSearch\Lucene\Search\Query\Wildcard;
use Inventory\Services\ArticleSearchService;
use Inventory\Services\AssetSearchService;
use Inventory\Services\SparePartsSearchService;
use Procurement\Model\PurchaseRequestCartItemTable;
use User\Model\UserTable;

use Inventory\Service\ItemSearchService;

class SearchController extends AbstractActionController {
	
	private $assetSearchService;
	private $sparePartSearchService;
	private $articleSearchService;
	private $purchaseRequestCartItemTable;
	private $authService;
	private $userTable;
	
	protected $itemSearchService;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function optimizeItemIndexAction() {
		
		$results = $this->itemSearchService->optimizeIndex();
		
		return new ViewModel ( array (
				'message' => $results
			) );
	}
	
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function itemAction() {
		
		$q = $this->params ()->fromQuery ( 'q' );
		
		if ($q !== "") {
			$results = $this->itemSearchService->searchAllItem($q );
		} else {
			$results = [
					"message"=> "",
					"query"=> null,
					"hits"=>null,
			];
		}
		
		//var_dump($results);
		return new ViewModel ( array (
				'message' => $results["message"],
				"query"=> $results["query"],
				'hits' => $results["hits"],
		) );
	}
	
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function assetItemAction() {
		
		$q = $this->params ()->fromQuery ( 'q' );
		
		if ($q !== "") {
			$results = $this->itemSearchService->searchAssetItem($q );
		} else {
			$results = [
					"message"=> "",
					"hits"=>null,
			];
		}
		
		//var_dump($results);
		return new ViewModel ( array (
				'message' => $results["message"],
				'hits' => $results["hits"]
		) );
	}
	
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function sparepartItemAction() {
		
		$q = $this->params ()->fromQuery ( 'q' );
		
		if ($q !== "") {
			$results = $this->itemSearchService->searchSPItem($q );
		} else {
			$results = [
					"message"=> "",
					"hits"=>null,
			];
		}
		
		//var_dump($results);
		return new ViewModel ( array (
				'message' => $results["message"],
				'hits' => $results["hits"]
		) );
	}
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createIndexAction() {
		$message = $this->itemSearchService->createItemIndex();
		
		return new ViewModel ( array (
				'message' => $message
		) );
		
	}
	
	/**
	 *
	 * @deprecated
	 *
	 * @return \Zend\View\Model\ViewModel|\Zend\Stdlib\ResponseInterface
	 */
	public function assetAction() {
		
		// $query = $this->params ()->fromQuery ( 'query' );
		$q = $this->params ()->fromQuery ( 'query' );
		$json = ( int ) $this->params ()->fromQuery ( 'json' );
		
		if ($q == '') {
			return new ViewModel ( array (
					'hits' => null 
			) );
		}
		
		if (strpos ( $q, '*' ) !== false) {
			$pattern = new Term ( $q );
			$query = new Wildcard ( $pattern );
			$hits = $this->assetSearchService->search ( $query );
		} else {
			$hits = $this->assetSearchService->search ( $q );
		}
		
		if ($json === 1) {
			
			$data = array ();
			
			foreach ( $hits as $key => $value ) {
				$n = ( int ) $key;
				$data [$n] ['id'] = $value->asset_id;
				$data [$n] ['name'] = $value->name;
				$data [$n] ['tag'] = $value->tag;
			}
			
			$response = $this->getResponse ();
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
			$response->setContent ( json_encode ( $data ) );
			return $response;
		}
		
		return new ViewModel ( array (
				'query' => $q,
				'hits' => $hits 
		) );
	}
	
	/**
	 *
	 * @deprecated
	 *
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function asset1Action() {
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/text' );
		$response->setContent ( 'Test' );
		return $response;
	}
	
	/**
	 *
	 * @deprecated
	 *
	 */
	public function sparepartAction() {
		
		// $query = $this->params ()->fromQuery ( 'query' );
		$q = $this->params ()->fromQuery ( 'query' );		
		$json = (int) $this->params ()->fromQuery ( 'json' );
		
	
		if($q==''){
			return new ViewModel ( array (
					'hits' => null,	
			));
		}
		
		if (strpos($q,'*') !== false) {
			$pattern = new Term($q);
			$query = new Wildcard($pattern);
			$hits = $this->sparePartSearchService->search($query);
		
		}else{
			$hits = $this->sparePartSearchService->search($q);
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
		));
	}
	
	/**
	 * @deprecated
	 * @return \Zend\View\Model\ViewModel|\Zend\Stdlib\ResponseInterface
	 */
	public function articleAction() {
		
		//$query = $this->params ()->fromQuery ( 'query' );
		$user_id = $this->UserPlugin()->getUser()['id'];
		$user = $this->userTable->getUserDepartment($user_id);
		
		if(!$user == null){
			$department = $user->department_id;
		}else{
			$department=0;
		}
		
		//var_dump($department);
	
		$q = $this->params ()->fromQuery ( 'query' );
		$json = (int) $this->params ()->fromQuery ( 'json' );
	
	
		if($q==''){
			return new ViewModel ( array (
					'hits' => null,
			));
		}
	
		if (strpos($q,'*') != false) {
			$pattern = new Term($q);
			$query = new Wildcard($pattern);
			$hits = $this->articleSearchService->search($query,$department);
			
		}else{
			$hits = $this->articleSearchService->search($q,$department);
		}
	
	
		if ($json === 1){
	
			$data = array();
	
			foreach ($hits as $key => $value)
			{
				$n = (int)$key;
				$data[$n]['id'] = $value->article_id;
				$data[$n]['name'] =  $value->name;
				$data[$n]['description'] =  $value->description;
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
		));
	}
	
	/**
	 * @deprecated
	 * @return \Zend\View\Model\ViewModel|\Zend\Stdlib\ResponseInterface
	 */
	public function allArticleAction() {
	
		$q = $this->params ()->fromQuery ( 'query' );
		$json = (int) $this->params ()->fromQuery ( 'json' );
	
		if($q==''){
			return new ViewModel ( array (
					'hits' => null,
			));
		}
	
		if (strpos($q,'*') != false) {
			$pattern = new Term($q);
			$query = new Wildcard($pattern);
			$hits = $this->articleSearchService->search($query,0);
				
		}else{
			$hits = $this->articleSearchService->search($q,0);
		}
	
	
		if ($json === 1){
	
			$data = array();
	
			foreach ($hits as $key => $value)
			{
				$n = (int)$key;
				$data[$n]['id'] = $value->article_id;
				$data[$n]['name'] =  $value->name;
				$data[$n]['description'] =  $value->description;
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
		));
	}
	
	public function getAssetSearchService() {
		return $this->assetSearchService;
	}
	
		public function setAssetSearchService(AssetSearchService $assetSearchService) {
		$this->assetSearchService = $assetSearchService;
		return $this;
	}
	
	public function getSparePartSearchService() {
		return $this->sparePartSearchService;
	}
	
	public function setSparePartSearchService(SparePartsSearchService $sparePartSearchService) {
		$this->sparePartSearchService = $sparePartSearchService;
		return $this;
	}
	
	public function getArticleSearchService() {
		return $this->articleSearchService;
	}
	
	public function setArticleSearchService(ArticleSearchService $articleSearchService) {
		$this->articleSearchService = $articleSearchService;
		return $this;
	}
	
	
	public function getPurchaseRequestCartItemTable() {
		return $this->purchaseRequestCartItemTable;
	}
	
	public function setPurchaseRequestCartItemTable($purchaseRequestCartItemTable) {
		$this->purchaseRequestCartItemTable = $purchaseRequestCartItemTable;
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
	public function setUserTable(UserTable $userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getItemSearchService() {
		return $this->itemSearchService;
	}
	public function setItemSearchService(ItemSearchService $itemSearchService) {
		$this->itemSearchService = $itemSearchService;
		return $this;
	}
	
	
	

}



