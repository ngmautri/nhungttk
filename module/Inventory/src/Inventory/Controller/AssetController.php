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
use Inventory\Model\AssetCategory;
use Inventory\Model\MLAAsset;
use Inventory\Model\AssetPicture;
use MLA\Paginator;
use MLA\Files;
use Zend\Barcode\Barcode;

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
	
	public function barcodeAction() {
	
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
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
		
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
				$input->comment = $request->getPost ( 'comment' );
				
				$newId = $this->getMLAAssetTable ()->add ( $input );
				$asset_dir = $this->getAssetService ()->createAssetFolderById ( $newId );
				$pictures_dir = $asset_dir . DIRECTORY_SEPARATOR . "pictures";
				
				$pictureUploadListener = $this->getServiceLocator()->get ( 'Inventory\Listener\PictureUploadListener');
				$this->getEventManager()->attachAggregate ( $pictureUploadListener );
				
				
				foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
					if ($error == UPLOAD_ERR_OK) {
						$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
						
						$ext = pathinfo($_FILES ["pictures"] ["name"] [$key], PATHINFO_EXTENSION);					
						$name = Files::generate_random_string().'_'.md5(uniqid(microtime())).'.'.strtolower ($ext);
											
						$ftype = $_FILES ["pictures"] ["type"] [$key];
						move_uploaded_file ( $tmp_name, "$pictures_dir/$name" );
						
						// add pictures
						$pic = new AssetPicture ();
						$pic->url = "$pictures_dir/$name";
						$pic->filetype = $ftype;
						$pic->asset_id = $newId;
						$pic->filename = $name;
						$pic->folder = $pictures_dir;
											
						
						$this->getAssetPictureTable ()->add ( $pic );
						
						// trigger uploadPicture
						$this->getEventManager()->trigger(
								'uploadPicture', __CLASS__, array('picture_name' => $name,'pictures_dir'=>$pictures_dir)
						);
					}
				}
				
				$redirectUrl  = $request->getPost ( 'redirectUrl' );
				$this->redirect()->toUrl($redirectUrl);	
			}
		}
		
		return new ViewModel ( array (
				'category_id' => $this->params ()->fromQuery ( 'category_id' ),
				'category' => $this->params ()->fromQuery ( 'category' ),
				'redirectUrl'=>$redirectUrl,
				
		) );
	}
	public function editAction() {
		
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
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
			$input->comment = $request->getPost ( 'comment' );
				
			
			$this->getMLAAssetTable ()->update ( $input,$input->id );
			$asset_dir = $this->getAssetService ()->getAssetPath($input->id);
			$pictures_dir = $asset_dir . DIRECTORY_SEPARATOR . "pictures";
			
			
			$pictureUploadListener = $this->getServiceLocator()->get ( 'Inventory\Listener\PictureUploadListener');
			$this->getEventManager()->attachAggregate ( $pictureUploadListener );
			
			
			foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
				if ($error == UPLOAD_ERR_OK) {
					$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
					
					$ext = pathinfo($_FILES ["pictures"] ["name"] [$key], PATHINFO_EXTENSION);					
					$name = Files::generate_random_string().'_'.md5(uniqid(microtime())).'.'.strtolower ($ext);
											
					$ftype = $_FILES ["pictures"] ["type"] [$key];
					move_uploaded_file ( $tmp_name, "$pictures_dir/$name" );
					
					// add pictures
					$pic = new AssetPicture ();
					$pic->url = "$pictures_dir/$name";
					$pic->filetype = $ftype;
					$pic->asset_id = $input->id;
					$pic->filename = $name;
					$pic->folder = $pictures_dir;
					$this->getAssetPictureTable ()->add ( $pic );
					
					// trigger uploadPicture
					$this->getEventManager()->trigger(
							'uploadPicture', __CLASS__, array('picture_name' => $name,'pictures_dir'=>$pictures_dir)
							);
				}
			}
			
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
			$this->redirect()->toUrl($redirectUrl);	
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$asset = $this->getMLAAssetTable ()->get ( $id );
		
		return new ViewModel ( array (
				'asset' => $asset,
				'redirectUrl'=>$redirectUrl,
		) );
	}
	
	public function showAction() {
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$asset = $this->getMLAAssetTable ()->get ( $id );
		
		$pictures = $this->getAssetPictureTable ()->getAssetPicturesById ( $id );
		
		return new ViewModel ( array (
				'asset' => $asset,
				'pictures' => $pictures 
		)
		 );
	}
	
	
	
	
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
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
		
		if ($request->isPost ()) {
			
			$assetType = new AssetCategory ();
			$assetType->category = $request->getPost ( 'category' );
			$assetType->description = $request->getPost ( 'description' );
			$this->getAssetCategoryTable ()->add ( $assetType );
			
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
			$this->redirect()->toUrl($redirectUrl);	
			
		}
		
		return new ViewModel ( array (
				'redirectUrl'=>$redirectUrl,
			) );
	}
	
	public function editcategoryAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
		
		if ($request->isPost ()) {
			
			$assetType = new AssetCategory ();
			$assetType->category = $request->getPost ( 'category' );
			$assetType->description = $request->getPost ( 'description' );
			
			$this->getAssetCategoryTable ()->update ( $assetType, $request->getPost ( 'id' ) );
			
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
			$this->redirect()->toUrl($redirectUrl);	
			
		} else {
			
			$id = ( int ) $this->params ()->fromQuery ( 'id' );
			$category = $this->getAssetCategoryTable ()->get ( $id );
			
			return new ViewModel ( array (
					'category' => $category,
					'redirectUrl'=>$redirectUrl,						
			) );
		}
	}
	
	public function picturesAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'asset_id' );
		$sp = $this->getMLAAssetTable()->get ( $id );
		$pictures = $this->getAssetPictureTable()->getAssetPicturesById( $id );
	
		return new ViewModel ( array (
				'asset' => $sp,
				'pictures' => $pictures,
		) );
	}
	
	public function deletecategoryAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
		
		if ($request->isPost ()) {
			
			$del = $request->getPost ( 'delete_confirmation', 'NO' );
			
			if ($del === 'YES') {
				$this->getAssetCategoryTable ()->delete ( $request->getPost ( 'id' ) );
			}
			
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
			$this->redirect()->toUrl($redirectUrl);	
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$category = $this->getAssetCategoryTable ()->get ( $id );
		
		return new ViewModel ( array (
				'category' => $category,
				'redirectUrl'=>$redirectUrl,
				
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
