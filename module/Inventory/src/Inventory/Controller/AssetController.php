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
use Inventory\Model\MLAAsset;
use Inventory\Model\AssetPicture;
use MLA\Paginator;

class AssetController extends AbstractActionController {
	public $assetCategoryTable;
	public $assetGroupTable;
	public $mlaAssetTable;
	public $assetPictureTable;
	public $assetService;
	public $authService;
	public $massage = 'NULL';
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 * Barcode
	 */
	public function barcodeAction() {
		$request = $this->getRequest ();
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$asset = $this->getMLAAssetTable ()->get ( $id );
		
		// Only the text to draw is required
		$barcodeOptions = array (
				'text' => $asset->tag 
		);
		
		// No required options
		$rendererOptions = array ();
		
		// Draw the barcode in a new image,
		Barcode::factory ( 'code39', 'image', $barcodeOptions, $rendererOptions )->render ();
	}
	public function addAction() {
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			
			$request = $this->getRequest ();
			if ($request->isPost ()) {
				
				$input = new MLAAsset ();
				$input->name = $request->getPost ( 'name' );
				$input->description = $request->getPost ( 'description' );
				$input->category_id = $request->getPost ( 'category_id' );
				$input->group_id = $request->getPost ( 'group_id' );
				
				$input->tag = $request->getPost ( 'tag' );
				$input->brand = $request->getPost ( 'brand' );
				$input->model = $request->getPost ( 'model' );
				$input->serial = $request->getPost ( 'serial' );
				$input->origin = $request->getPost ( 'origin' );
				$input->received_on = $request->getPost ( 'received_on' );
				
				$input->location = $request->getPost ( 'location' );
				
				$newId = $this->getMLAAssetTable ()->add ( $input );
				$asset_dir = $this->getAssetService ()->createAssetFolderById ( $newId );
				$pictures_dir = $asset_dir . DIRECTORY_SEPARATOR . "pictures";
				
				$files = $request->getFiles ()->toArray ();
				
				foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
					if ($error == UPLOAD_ERR_OK) {
						$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
						$name = $_FILES ["pictures"] ["name"] [$key];
						$ftype = $_FILES ["pictures"] ["type"] [$key];
						move_uploaded_file ( $tmp_name, "$pictures_dir/$name" );
						
						// add pictures
						$pic = new AssetPicture ();
						$pic->url = "$pictures_dir/$name";
						$pic->filetype = $ftype;
						$pic->asset_id = $newId;
						$this->getAssetPictureTable ()->add ( $pic );
					}
				}				
				return $this->redirect ()->toRoute ( 'assetcategory' );
			}
		}
		
		return new ViewModel ( array (
				'category_id' => $this->params ()->fromQuery ( 'category_id' ),
				'category' => $this->params ()->fromQuery ( 'category' ) 
		) );
	}
	public function editAction() {
		
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			
			$input = new MLAAsset ();
			$input->id = $request->getPost ( 'id' );
			$input->name = $request->getPost ( 'name' );
			$input->description = $request->getPost ( 'description' );
			$input->category_id = $request->getPost ( 'category_id' );
			$input->group_id = $request->getPost ( 'group_id' );
			
			$input->tag = $request->getPost ( 'tag' );
			$input->brand = $request->getPost ( 'brand' );
			$input->model = $request->getPost ( 'model' );
			$input->serial = $request->getPost ( 'serial' );
			$input->origin = $request->getPost ( 'origin' );
			$input->received_on = $request->getPost ( 'received_on' );
			
			$input->location = $request->getPost ( 'location' );
			
			$this->getMLAAssetTable ()->update ( $input,$input->id );
			$asset_dir = $this->getAssetService ()->getAssetPath($input->id);
			$pictures_dir = $asset_dir . DIRECTORY_SEPARATOR . "pictures";
			
			$files = $request->getFiles ()->toArray ();
			
			foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
				if ($error == UPLOAD_ERR_OK) {
					$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
					$name = $_FILES ["pictures"] ["name"] [$key];
					$ftype = $_FILES ["pictures"] ["type"] [$key];
					move_uploaded_file ( $tmp_name, "$pictures_dir/$name" );
					
					// add pictures
					$pic = new AssetPicture ();
					$pic->url = "$pictures_dir/$name";
					$pic->filetype = $ftype;
					$pic->asset_id = $input->id;
					$this->getAssetPictureTable ()->add ( $pic );
				}
			}
			
			return $this->redirect ()->toRoute ( 'assetcategory' );
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$asset = $this->getMLAAssetTable ()->get ( $id );
		
		return new ViewModel ( array (
				'asset' => $asset
		) );
	}
	
	public function showAction() {
		$request = $this->getRequest ();
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$asset = $this->getMLAAssetTable ()->get ( $id );
		
		$pictures = $this->getAssetPictureTable ()->getAssetPicturesById ( $id );
		
		return new ViewModel ( array (
				'asset' => $asset,
				'pictures' => $pictures 
		)
		 );
	}
	
	/**
	 */
	public function categorydetailAction() {
		$categeory_id = $this->params ()->fromQuery ( 'category_id' );
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 20;
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
		
		$assets = $this->getMLAAssetTable ()->getAssetsByCategoryID ( $categeory_id );
		$totalResults = $assets->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$assets = $this->getMLAAssetTable ()->getLimitAssetsByCategoryID ( $categeory_id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'total_asset' => $totalResults,
				'category_id' => $categeory_id,
				'category' => $this->params ()->fromQuery ( 'category' ),
				'assets' => $assets,
				'paginator' => $paginator 
		) );
	}
	public function addcategoryAction() {
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			
			$assetType = new AssetCategory ();
			$assetType->category = $request->getPost ( 'category' );
			$assetType->description = $request->getPost ( 'description' );
			$this->getAssetCategoryTable ()->add ( $assetType );
			
			return $this->redirect ()->toRoute ( 'assetcategory' );
		}
	}
	public function editcategoryAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$assetType = new AssetCategory ();
			$assetType->category = $request->getPost ( 'category' );
			$assetType->description = $request->getPost ( 'description' );
			
			$this->getAssetCategoryTable ()->update ( $assetType, $request->getPost ( 'id' ) );
			
			return $this->redirect ()->toRoute ( 'assetcategory' );
		} else {
			
			$id = ( int ) $this->params ()->fromQuery ( 'id' );
			$category = $this->getAssetCategoryTable ()->get ( $id );
			
			return new ViewModel ( array (
					'category' => $category 
			) );
		}
	}
	public function deletecategoryAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$del = $request->getPost ( 'delete_confirmation', 'NO' );
			
			if ($del === 'YES') {
				$this->getAssetCategoryTable ()->delete ( $request->getPost ( 'id' ) );
			}
			
			return $this->redirect ()->toRoute ( 'assetcategory' );
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$category = $this->getAssetCategoryTable ()->get ( $id );
		
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
	
	// get AssetTable
	private function getMLAAssetTable() {
		if (! $this->mlaAssetTable) {
			$sm = $this->getServiceLocator ();
			$this->mlaAssetTable = $sm->get ( 'Inventory\Model\MLAAssetTable' );
		}
		return $this->mlaAssetTable;
	}
	
	// get AssetService
	private function getAssetService() {
		if (! $this->assetService) {
			$sm = $this->getServiceLocator ();
			$this->assetService = $sm->get ( 'Inventory\Services\AssetService' );
		}
		return $this->assetService;
	}
	
	// get AssetPictureTable
	private function getAssetPictureTable() {
		if (! $this->assetPictureTable) {
			$sm = $this->getServiceLocator ();
			$this->assetPictureTable = $sm->get ( 'Inventory\Model\AssetPictureTable' );
		}
		return $this->assetPictureTable;
	}
}
