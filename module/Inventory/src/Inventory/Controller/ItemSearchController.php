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
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use Inventory\Service\ItemSearchService;

/*
 * Control Panel Controller
 */
class ItemSearchController extends AbstractActionController {
	protected $doctrineEM;
	protected $itemSearchService;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		return new ViewModel ( array () );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function autocompleteAction() {
		/* retrieve the search term that autocomplete sends */
		$q = trim ( strip_tags ( $_GET ['term'] ) );
		//$q = $this->params ()->fromQuery ( 'q' );
		
		$a_json = array ();
		$a_json_row = array ();
		
		if ($q !== "") {
			$results = $this->itemSearchService->searchAll( $q );
			if (count ( $results ) > 0) {
				$n=1;
				foreach ( $results['hits'] as $a ) {
					$a_json_row ["id"] = $a->item_id;
					$a_json_row ["value"] = $a->item_name . ': ' .$a->item_sku ;
					$a_json[]=$a_json_row;
					$n++;
				}
			}
		}
		//var_dump($a_json);
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
		$response->setContent ( json_encode ( $a_json ) );
		return $response;
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function doAction() {
		$q = $this->params ()->fromQuery ( 'q' );
		
		if ($q !== "") {
			$results = $this->itemSearchService->searchAll ( $q );
		} else {
			$results = [ 
					"message" => "",
					"hits" => null 
			];
		}
		
		// var_dump($results);
		return new ViewModel ( array (
				'message' => $results ["message"],
				'hits' => $results ["hits"] 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function do1Action() {
		$request = $this->getRequest ();
		
		// accepted only ajax request
		
		/* if (! $request->isXmlHttpRequest ()) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		} */
		
		$this->layout ( "layout/user/ajax" );
		
		$q = $this->params ()->fromQuery ( 'q' );
		$context = $this->params ()->fromQuery ( 'context' );
		$target_id= $this->params ()->fromQuery ( 'target_id' );
		$target_name= $this->params ()->fromQuery ( 'target_name' );
		
		$results = [ 
				"message" => "",
				"hits" => null,
				'context' => $context,
				'target_id' => $target_id,
				'target_name' => $target_name,
		];
		
		if ($q !== null) {
			if ($q !== "") {
				$results = $this->itemSearchService->searchAll ( $q );
			}
		}
		
		// var_dump($results);
		return new ViewModel ( array (
				'message' => $results ["message"],
				'hits' => $results ["hits"],
				'context' => $context,
				'target_id' => $target_id,
				'target_name' => $target_name,
				
		) );
	}
	
	/**
	 */
	public function createIndexAction() {
		$result = $this->vendorSearchService->createVendorIndex ();
		return new ViewModel ( array (
				'result' => $result 
		) );
	}
	
	/**
	 *
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	
	/**
	 *
	 * @param EntityManager $doctrineEM        	
	 * @return \BP\Controller\VendorController
	 */
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
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
