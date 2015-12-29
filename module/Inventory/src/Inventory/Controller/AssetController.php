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
use Zend\Barcode\Barcode;
use Inventory\Model\AssetCategory;

class AssetController extends AbstractActionController {
	public $assetCategoryTable;
	public $assetGroupTable;
	public $authService;
	public $massage = 'NULL';
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	public function addcategoryAction() {
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			
			 $assetType = new AssetCategory();
			 $assetType->category = $request->getPost('category');
			 $assetType->description =  $request->getPost('description');
			 $this->getAssetCategoryTable()->add($assetType);
			
			
			return $this->redirect ()->toRoute ( 'assetcategory');
		}
	}
	
	public function editcategoryAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$assetType = new AssetCategory ();
			$assetType->category = $request->getPost ( 'category' );
			$assetType->description = $request->getPost ( 'description' );
			
			$this->getAssetCategoryTable ()->update( $assetType, $request->getPost ( 'id' ) );
			
			return $this->redirect ()->toRoute ( 'assetcategory' );
		} else {
		
			$id = (int) $this->params()->fromQuery('id');			
			$category = $this->getAssetCategoryTable ()->get ($id);
			
			return new ViewModel ( array (
					'category' => $category
			) );			
		}
	}
	
	
	public function deletecategoryAction() {
		
		
		
		
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			
			$del = $request->getPost('delete_confirmation', 'NO');
		
			if ($del === 'YES') {
				$this->getAssetCategoryTable ()->delete ($request->getPost ( 'id' ));
			}
		
			return $this->redirect()->toRoute('assetcategory');
		}
		
		$id = (int) $this->params()->fromQuery('id');
		$category = $this->getAssetCategoryTable ()->get ($id);
			
		return new ViewModel ( array (
				'category' => $category
		) );
		
	}
	
	public function categoryAction() {
		return new ViewModel ( array (
				'assetCategories' => $this->getAssetCategoryTable ()->fetchAll () 
		) );
	}
	public function groupAction() {
		return new ViewModel ( array (
				'assetGroups' => $this->getAssetGroupTable ()->fetchAll () 
		) );
	}
	
	// get AssetTable
	private function getAssetCategoryTable() {
		if (! $this->assetCategoryTable) {
			$sm = $this->getServiceLocator ();
			$this->assetCategoryTable = $sm->get ( 'Inventory\Model\AssetCategoryTable' );
		}
		return $this->assetCategoryTable;
	}
	
	// get AssetTable
	private function getAssetGroupTable() {
		if (! $this->assetGroupTable) {
			$sm = $this->getServiceLocator ();
			$this->assetGroupTable = $sm->get ( 'Inventory\Model\AssetGroupTable' );
		}
		return $this->assetGroupTable;
	}
}
