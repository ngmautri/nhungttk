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


class SearchController extends AbstractActionController {
	
	protected 	 $assetSearchService;
	protected 	 $sparePartSearchService;
	
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	
	}
	
	/*
	 * Defaul Action
	 */
	public function assetAction() {
		
		//$query = $this->params ()->fromQuery ( 'query' );
		
		$query = $this->params ()->fromQuery ( 'query' );
		
		
		if($query==''){
			return new ViewModel ( array (
					'hits' => null,
			));
		}
				
		$hits = $this->getAssetSearchService()->search($query);
		return new ViewModel ( array (
				'hits' => $hits,
		));
	}
	
	public function sparepartAction() {
	
		//$query = $this->params ()->fromQuery ( 'query' );
	
		$query = $this->params ()->fromQuery ( 'query' );
	
	
		if($query==''){
			return new ViewModel ( array (
					'hits' => null,
			));
		}
	
		$hits = $this->getSparePartSearchService()->search($query);
		return new ViewModel ( array (
				'hits' => $hits,
		));
	}
	

	
	private function getAssetSearchService() {
		if (! $this->assetSearchService) {
			$sm = $this->getServiceLocator ();
			$this->assetSearchService = $sm->get ( 'Inventory\Services\AssetSearchService' );
		}
		return $this->assetSearchService;
	}
	
	private function getSparePartSearchService() {
		if (! $this->sparePartSearchService) {
			$sm = $this->getServiceLocator ();
			$this->getSparePartSearchService = $sm->get ( 'Inventory\Services\SparePartsSearchService' );
		}
		return $this->getSparePartSearchService;
	}
	
}



