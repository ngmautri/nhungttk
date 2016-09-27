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

use MLA\Paginator;
use MLA\Files;

use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\Wildcard;

use Inventory\Model\MLAAssetTable;
use Inventory\Model\AssetCategoryTable;
use Inventory\Model\AssetCountingTable;
use Inventory\Model\AssetCountingItemTable;
use Inventory\Model\AssetCounting;

use Inventory\Services\AssetSearchService;
use Inventory\Services\AssetService;

use Inventory\Model\AssetCountingItem;

use Inventory\Model\AssetPictureTable;
use Inventory\Model\AssetPicture;

class CountController extends AbstractActionController {
	protected $authService;
	protected $SmtpTransportService;
	
	protected $assetSearchService;
	protected $assetService;

	protected $userTable;	
	protected $assetTable;
	protected $assetPictureTable;
	protected $assetCategoryTable;
	
	protected $assetCountingTable;
	protected $assetCountingItemTable;
	
	protected $message = 'NULL';
	protected $locations = array("LINE-01", "LINE-02", 
			"LINE-03", "LINE-04",
			"LINE-05", "LINE-06",
			"LINE-07", "LINE-08",
			"LINE-08", "LINE-A",
			"LINE-SPE", "CUTTING",
			"WORK-SHOP", "ADMIN","CANTEEN","NOT-USED", "OTHER",
				
	);
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		
			return new ViewModel ( array (
		) ); 
	}
	
	
	public function createAssetCountingAction() {
		
		$request = $this->getRequest ();
		$user = $this->authService->getIdentity();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
			
		if ($request->isPost ()) {
				
			if ($request->isPost ()) {
				$redirectUrl  = $request->getPost ( 'redirectUrl' );
		
				$input = new AssetCounting ();
				$input->name = $request->getPost ( 'name' );
				$input->description = $request->getPost ( 'description' );
				$input->start_date = $request->getPost ( 'start_date' );
				$input->end_date = $request->getPost ( 'end_date' );
				$input->asset_cat_id = $request->getPost ( 'asset_category' );
				$input->status = "STARTED";
				$input->created_by = $user;
		
				$errors = array();
				
				// validator.
				$validator = new Date ();
				
				$errors = array();
				
				if (! $validator->isValid ( $input->start_date )) {
					$errors [] = 'Start date format is not correct!';
				}else{
					
					if( $input->start_date < date ( 'Y-m-d' ) ){
						$errors [] = 'Start date is in the past';
					}
				}

				if (! $validator->isValid ( $input->end_date )) {
					
					$errors [] = 'End date format is not correct!';
				}else{
					
					if( date_create($input->start_date) > date_create($input->end_date)){
						$errors [] = 'End date format is small then the Start date';
					}
				}
				
				if ($input->name ==''){
					$errors [] = 'Please givename';
				}
					
		
				if (count ( $errors ) > 0) {
					
					$asset_cats = $this->assetCategoryTable->fetchAll();
					
					return new ViewModel ( array (
							'redirectUrl'=>$redirectUrl,
							'user' =>$user,
							'asset_cats'=> $asset_cats,
								'errors' => $errors,
					) );
				}
				
				$this->assetCountingTable->add($input);
				
				$this->redirect()->toUrl($redirectUrl);
			}
		}
		
		$asset_cats = $this->assetCategoryTable->fetchAll();
		
		return new ViewModel ( array (
							'redirectUrl'=>$redirectUrl,
							'user' =>$user,
							'asset_cats'=> $asset_cats,
							'errors' => null,
		));
	}
	
	
	public function listAssetCountingAction() {
		$countings = $this->assetCountingTable->getCountings();
		
		return new ViewModel ( array (
				'countings'=> $countings,
		));
	}
	
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function showAssetCountingAction() {
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		
		$counting = $this->assetCountingTable->get($id);
		$asset_cat_id = (int) $counting->asset_cat_id;
		$items = $this->assetCountingItemTable->getCountedItems($id,$asset_cat_id);
		$total_counted = $this->assetCountingItemTable->getTotalCounted($id);
		$total_to_count = $this->assetCountingItemTable->getTotalToCount($asset_cat_id);
		
		return new ViewModel ( array (
				'counting'=> $counting,
				'items'=> $items,
				'total_counted' =>$total_counted,
				'total_to_count' =>$total_to_count,
		));
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function printAssetCountingAction() {
	
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
	
		$counting = $this->assetCountingTable->get($id);
		$asset_cat_id = (int) $counting->asset_cat_id;
		$items = $this->assetCountingItemTable->getCountedItems($id,$asset_cat_id);
		$total_counted = $this->assetCountingItemTable->getTotalCounted($id);
		$total_to_count = $this->assetCountingItemTable->getTotalToCount($asset_cat_id);
	
		$this->layout("layout/inventory/print");
		return new ViewModel ( array (
				'counting'=> $counting,
				'items'=> $items,
				'total_counted' =>$total_counted,
				'total_to_count' =>$total_to_count,
		));
	}
	
	public function selectLocationAction() {
	
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$counting = $this->assetCountingTable->get($id);
	
		return new ViewModel ( array (
				'counting'=> $counting,
				'locations'=>$this->locations,
		));
	}
	
	
	public function assetAction() {
	
		$id = $this->params ()->fromQuery ( 'id' );
		$counting = $this->assetCountingTable->get($id);
		
		$location = $this->params ()->fromQuery ( 'location' );
		$updated_location = $this->params ()->fromQuery ( '$updated_location' );
		
		$q = $this->params ()->fromQuery ( 'query' );
		$json = (int) $this->params ()->fromQuery ( 'json' );
	
		if($q==''){
			return new ViewModel ( array (
				'hits' => null,
				'counting' => $counting,
				'counting_id' => $id,
				'location'=>$location,
				'updated_location'=>$updated_location,
						
						
			));
		}
	
		if (strpos($q,'*') !== false) {
			$pattern = new Term($q);
			$query = new Wildcard($pattern);
			$hits = $this->assetSearchService->search($query . ' AND catetory_id: ' . $id);
	
		}else{
			$hits = $this->assetSearchService->search($q. ' AND catetory_id:' . $id);
		}
	
		
		$data = array();
		
		foreach ($hits as $key => $value)
		{
			$n = (int)$key;
			$data[$n]['id'] = $value->asset_id;
			$data[$n]['name'] =  $value->name;
			$data[$n]['tag'] =  $value->tag;
			$counted=$this->assetCountingItemTable->isAssetCounted($id, $value->asset_id);
			$counted=== true ? $data[$n]['status'] =  'COUNTED' : $data[$n]['status'] =  'TO COUNTED';
		
		}
		
	
		if ($json === 1){
				
	
				
				
			$response = $this->getResponse();
			$response->getHeaders()->addHeaderLine( 'Content-Type', 'application/json' );
			$response->setContent(json_encode($data));
			return $response;
		}
			
		return new ViewModel ( array (
				'query' => $q,
				'hits' => $data,
				'counting' => $counting,
				'counting_id' => $id,
				'location'=>$location,
				'updated_location'=>$updated_location,
				
		));
		
	}
	
	public function addCountingItem1Action() {
		
		$request = $this->getRequest ();
		$user = $this->authService->getIdentity();
		
		$counting_id = ( int ) $this->params ()->fromQuery ( 'id' );
		$asset_id = ( int ) $this->params ()->fromQuery ( 'asset_id' );
		
		$location = $this->params ()->fromQuery ( 'location' );
		
		if ($this->assetCountingItemTable->isAssetCounted($counting_id, $asset_id)){
			$asset =  $this->assetTable->get($asset_id);
			$counting = $this->assetCountingTable->get($counting_id);
			$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
				
			return new ViewModel ( array (
					'counting'=> $counting,
					'asset'=> $asset,
					'errors'=> null,
					'redirectUrl'=>$redirectUrl,
					'counted'=>true,
					'location'=>$location,
			));
		}
		
		if ($request->isPost ()) {
			
			$pictures = $_POST['pictures'];
			$id = $_POST['asset_id'];
			//$filetype = $_POST['filetype'];
			
			$input = new AssetCountingItem();
			$input->counting_id = $_POST['counting_id'];
			$input->asset_id =  $id;
			$input->location =   $_POST['updated_location'];
			$input->counted_by =  $user;
			
			$this->assetCountingItemTable->add($input);
					
			foreach ($pictures as $p){
				
				$filetype = $p[0];

				if(preg_match('/(jpg|jpeg)$/', $filetype)) {
					$ext = 'jpg';
				
				} else if (preg_match('/(gif)$/', $filetype)) {
					$ext = 'gif';
				} else if (preg_match('/(png)$/', $filetype)) {
					$ext = 'png';
				}
				
				$tmp_name = md5 ( $id . uniqid ( microtime () ) ) . '.' . $ext;
				
				// remove "data:image/png;base64,"
				$uri =  substr($p[1],strpos($p[1],",") +1 );
					
				// save to file
				file_put_contents($tmp_name, base64_decode($uri));
					
				$checksum = md5_file ( $tmp_name );
					
					
				$root_dir = $this->getAssetService ()->getPicturesPath ();
				
				$pictureUploadListener = $this->getServiceLocator ()->get ( 'Inventory\Listener\PictureUploadListener' );
				$this->getEventManager ()->attachAggregate ( $pictureUploadListener );
					
					
				if (! $this->getAssetPictureTable ()->isChecksumExits ( $id, $checksum )) {
					$name = md5 ( $id . $checksum . uniqid ( microtime () ) ) . '.' . $ext;
						
					$folder = $root_dir . DIRECTORY_SEPARATOR . $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
						
					if (! is_dir ( $folder )) {
						mkdir ( $folder, 0777, true ); // important
					}
						
					rename( $tmp_name, "$folder/$name" );
						
					// add pictures
					$pic = new AssetPicture ();
					$pic->url = "$folder/$name";
					$pic->filetype = $filetype;
					$pic->asset_id = $id;
					$pic->filename = "$name";
					$pic->folder = "$folder";
					$pic->checksum = $checksum;
					$pic->comments ="counted by " . $user;
						
					$this->getAssetPictureTable ()->add ( $pic );
						
					// trigger uploadPicture
					$this->getEventManager ()->trigger ( 'uploadPicture', __CLASS__, array (
							'picture_name' => $name,
							'pictures_dir' => $folder
					) );
				}
				
				
			}
			

			$data = array();
			$data['id'] =  $id;
			//$data['filetype'] =  $filetype;
				
			$response = $this->getResponse();
			$response->getHeaders()->addHeaderLine( 'Content-Type', 'application/json' );
			$response->setContent(json_encode($data));
			return $response;
			
		}
		
		$asset =  $this->assetTable->get($asset_id);
		$counting = $this->assetCountingTable->get($counting_id);
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
		return new ViewModel ( array (
				'counting'=> $counting,
				'asset'=> $asset,
				'errors'=> null,
				'redirectUrl'=>$redirectUrl,
				'counted'=>false,
				'locations'=>$this->locations,
				'location'=>$location,
		));
		
	}
	
	
	
	public function addCountingItemAction() {
	
		$request = $this->getRequest ();
		$user = $this->authService->getIdentity();
				
		$counting_id = ( int ) $this->params ()->fromQuery ( 'id' );
		$asset_id = ( int ) $this->params ()->fromQuery ( 'asset_id' );
		
		$location = $this->params ()->fromQuery ( 'location' );
		
		if ($this->assetCountingItemTable->isAssetCounted($counting_id, $asset_id)){
				$asset =  $this->assetTable->get($asset_id);
				$counting = $this->assetCountingTable->get($counting_id);
				$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
			
				return new ViewModel ( array (
						'counting'=> $counting,
						'asset'=> $asset,
						'errors'=> null,
						'redirectUrl'=>$redirectUrl,
						'counted'=>true,
						'location'=>$location,
				));
		}
		

		if ($request->isPost ()) {
		
			if ($request->isPost ()) {
				$redirectUrl  = $request->getPost ( 'redirectUrl' );
				
				$input = new AssetCountingItem();
				$input->counting_id = $request->getPost ( 'counting_id' );
				$input->asset_id = $request->getPost ( 'asset_id' );
				$input->location =  $request->getPost ( 'updated_location' );
				$input->counted_by =  $user;
						
				$this->assetCountingItemTable->add($input);
				
				
				//add pitures
				
				$root_dir = $this->assetService->getPicturesPath ();
				
				$pictureUploadListener = $this->getServiceLocator ()->get ( 'Inventory\Listener\PictureUploadListener' );
				$this->getEventManager ()->attachAggregate ( $pictureUploadListener );
				
				$id = $input->asset_id;
				
				foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
					if ($error == UPLOAD_ERR_OK) {
						$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
				
						$ext = strtolower ( pathinfo ( $_FILES ["pictures"] ["name"] [$key], PATHINFO_EXTENSION ) );
				
						if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png') {
								
							$checksum = md5_file ( $tmp_name );
								
							if (! $this->assetPictureTable->isChecksumExits ( $id, $checksum )) {
				
								$name = md5 ( $id . $checksum . uniqid ( microtime () ) ) . '.' . $ext;
								$folder = $root_dir . DIRECTORY_SEPARATOR . $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
				
								if (! is_dir ( $folder )) {
									mkdir ( $folder, 0777, true ); // important
								}
				
								$ftype = $_FILES ["pictures"] ["type"] [$key];
								move_uploaded_file ( $tmp_name, "$folder/$name" );
				
								// add pictures
								$pic = new AssetPicture ();
								$pic->url = "$folder/$name";
								$pic->filetype = $ftype;
								$pic->asset_id = $id;
								$pic->filename = "$name";
								$pic->folder = "$folder";
								$pic->checksum = $checksum;
								$pic->comments = 'uploaded by '. $user.' on asset checking '. $input->counting_id;
				
								$this->assetPictureTable->add ( $pic );
				
								// trigger uploadPicture
								$this->getEventManager ()->trigger ( 'uploadPicture', __CLASS__, array (
										'picture_name' => $name,
										'pictures_dir' => $folder
								) );
							}
						}
					}
				}
				
				$this->redirect()->toUrl($redirectUrl);
			}
		}
		
		$asset =  $this->assetTable->get($asset_id);
		$counting = $this->assetCountingTable->get($counting_id);
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
	
		return new ViewModel ( array (
				'counting'=> $counting,
				'asset'=> $asset,
				'errors'=> null,
				'redirectUrl'=>$redirectUrl,
				'counted'=>false,
				'locations'=>$this->locations,
				'location'=>$location,
		));
	}
	
	// SETTER AND GETTER
		
	public function getAuthService() {
		return $this->authService;
	}
	public function setAuthService($authService) {
		$this->authService = $authService;
		return $this;
	}
	public function getSmtpTransportService() {
		return $this->SmtpTransportService;
	}
	public function setSmtpTransportService($SmtpTransportService) {
		$this->SmtpTransportService = $SmtpTransportService;
		return $this;
	}
	public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable($userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getAssetTable() {
		return $this->assetTable;
	}
	public function setAssetTable(MLAAssetTable $assetTable) {
		$this->assetTable = $assetTable;
		return $this;
	}
	public function getAssetCategoryTable() {
		return $this->assetCategoryTable;
	}
	public function setAssetCategoryTable(AssetCategoryTable $assetCategoryTable) {
		$this->assetCategoryTable = $assetCategoryTable;
		return $this;
	}
	public function getAssetCountingTable() {
		return $this->assetCountingTable;
	}
	public function setAssetCountingTable(AssetCountingTable $assetCountingTable) {
		$this->assetCountingTable = $assetCountingTable;
		return $this;
	}
	public function getAssetCountingItemTable() {
		return $this->assetCountingItemTable;
	}
	public function setAssetCountingItemTable(AssetCountingItemTable $assetCountingItemTable) {
		$this->assetCountingItemTable = $assetCountingItemTable;
		return $this;
	}
	public function getMessage() {
		return $this->massage;
	}
	public function setMessage($message) {
		$this->message = $message;
		return $this;
	}
	
	
	public function getAssetSearchService() {
		return $this->assetSearchService;
	}
	public function setAssetSearchService(AssetSearchService $assetSearchService) {
		$this->assetSearchService = $assetSearchService;
		return $this;
	}
	public function getAssetService() {
		return $this->assetService;
	}
	public function setAssetService(AssetService $assetService) {
		$this->assetService = $assetService;
		return $this;
	}
	public function getAssetPictureTable() {
		return $this->assetPictureTable;
	}
	public function setAssetPictureTable(AssetPictureTable $assetPictureTable) {
		$this->assetPictureTable = $assetPictureTable;
		return $this;
	}
	
	
	
	
}
