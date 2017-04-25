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
use Zend\Barcode\Barcode;
use Application\Entity\NmtInventoryItem;
use Application\Entity\NmtInventoryItemPicture;
use User\Model\UserTable;
use MLA\Paginator;
use Application\Entity\NmtInventoryItemCategoryMember;
use Application\Entity\NmtInventoryItemDepartment;
use Inventory\Service\ItemSearchService;

/*
 * Control Panel Controller
 */
class ItemController extends AbstractActionController {
	protected $doctrineEM;
	protected $itemSearchService;
	protected $userTable;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		
		$message = $this->flashMessenger()->getSuccessMessages();
		$this->flashMessenger()->clearCurrentMessages();
		
		return new ViewModel ( array (
				'message' => $message,
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function showAction() {
		$request = $this->getRequest ();
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$redirectUrl = $request->getHeader ( 'Referer' )->getUri ();
		
		$item_id = ( int ) $this->params ()->fromQuery ( 'item_id' );
		$item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $item_id );
		$pictures = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPicture' )->findBy ( array (
				"item" => $item_id 
		) );
		return new ViewModel ( array (
				'item' => $item,
				'pictures' => $pictures,
				'back' => $redirectUrl 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addAction() {
		$request = $this->getRequest ();
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
				'email' => $this->identity () 
		) );
		
		if ($request->isPost ()) {
			
			$item_sku = $request->getPost ( 'item_sku' );
			$item_name = $request->getPost ( 'item_name' );
			$item_name_foreign = $request->getPost ( 'item_name_foreign' );
			$item_description = $request->getPost ( 'item_description' );
			
			$item_barcode = $request->getPost ( 'item_barcode' );
			$keywords = $request->getPost ( 'keywords' );
			
			$item_type = $request->getPost ( 'item_type' );
			$item_category_id = $request->getPost ( 'item_category_id' );
			$department_id = $request->getPost ( 'department_id' );
			
			$standard_uom_id = $request->getPost ( 'standard_uom_id' );
			$item_leadtime = $request->getPost ( 'item_leadtime' );
			
			$is_active = $request->getPost ( 'is_active' );
			$is_stocked = $request->getPost ( 'is_stocked' );
			$is_purchased = $request->getPost ( 'is_purchased' );
			$is_sale_item = $request->getPost ( 'is_sale_item' );
			$is_fixed_asset = $request->getPost ( 'is_fixed_asset' );
			
			$manufacturer_code = $request->getPost ( 'manufacturer_code' );
			$manufacturer_serial = $request->getPost ( 'manufacturer_serial' );
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			
			$errors = array ();
			
			if ($item_sku === '' or $item_sku === null) {
				$errors [] = 'Please give ID';
			}
			
			if ($item_name === '' or $item_name === null) {
				$errors [] = 'Please give item name';
			}
			
			if ($standard_uom_id=== '' or $standard_uom_id=== null) {
				$errors [] = 'Please give standard measurement!';
			}
			
			/*
			 * $r = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findBy ( array (
			 * 'itemSku' => $item_sku
			 * ) );
			 *
			 * if (count ( $r ) >= 1) {
			 * $errors [] = $item_sku . ' exists';
			 * }
			 */
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'errors' => $errors,
						'redirectUrl' => $redirectUrl 
				) );
			}
			
			// No Error
			try {
				$entity = new NmtInventoryItem ();
				$entity->setItemSku ( $item_sku );
				$entity->setItemName ( $item_name );
				$entity->setItemNameForeign ( $item_name_foreign );
				$entity->setItemDescription ( $item_description );
				$entity->setBarcode ( $item_barcode );
				$entity->setKeywords ( $keywords );
				$entity->setItemType ( $item_type );
				
				$uom = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationUom', $standard_uom_id );
				$entity->setStandardUom( $uom );
				
				$entity->setManufacturerCode ( $manufacturer_code );
				$entity->setManufacturerSerial ( $manufacturer_serial );
				
				$entity->setIsActive ( $is_active );
				$entity->setIsStocked ( $is_stocked );
				$entity->setIsPurchased ( $is_purchased );
				$entity->setIsSaleItem ( $is_sale_item );
				$entity->setIsFixedAsset ( $is_fixed_asset );
				
				$entity->setLeadTime ( $item_leadtime );
				
				$company_id = 1;
				$company = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCompany', $company_id );
				$entity->setCompany ( $company );
				
				$entity->setCreatedOn ( new \DateTime () );
				$entity->setCreatedBy ( $u );
				
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				$new_id = $entity->getId ();
				
				$new_item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $new_id );
				
				// add category member
				$entity = new NmtInventoryItemCategoryMember ();
				
				$category = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItemCategory', $item_category_id );
				$entity->setItem ( $new_item );
				$entity->setCategory ( $category );
				
				$entity->setCreatedBy ( $u );
				$entity->setCreatedOn ( new \DateTime () );
				
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				
				// add department member
				$department = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationDepartment', $department_id );
				$entity = new NmtInventoryItemDepartment ();
				$entity->setDepartment ( $department );
				$entity->setItem ( $new_item );
				
				$entity->setCreatedBy ( $u );
				$entity->setCreatedOn ( new \DateTime () );
				
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				
				// update search index.
				$this->itemSearchService->addDocument ( $new_item,true);
				
			} catch ( Exception $e ) {
				return new ViewModel ( array (
						'errors' => $e->getMessage (),
						'redirectUrl' => $redirectUrl 
				) );
			}
			
			$this->flashMessenger()->addSuccessMessage("Item ". $item_name . " has been created sucessfully");
			
			$this->redirect ()->toUrl ( $redirectUrl );
		}
		
		return new ViewModel ( array (
				'errors' => null,
				'redirectUrl' => $redirectUrl 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
		$sort = array ();
		$criteria = array ();
		
		$sort_by = $this->params ()->fromQuery ( 'sort_by' );
		$item_status = $this->params ()->fromQuery ( 'item_status' );
		$item_type = $this->params ()->fromQuery ( 'item_type' );
		
		if ($item_status == null) :
			$item_status = "Activated";
		
		endif;
		
		if ($sort_by == null) :
			$sort_by = "itemName";		
		endif;
		
		$sort = array (
				$sort_by => "ASC" 
		);
		
		if (! $item_type == null) {
			$criteria = array (
					"itemType" => $item_type 
			);
		}
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 15;
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
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findBy ( $criteria, $sort );
		$total_records = count ( $list );
		$paginator = null;
		
		if ($total_records > $resultsPerPage) {
			$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findBy ( $criteria, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		// $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
		// var_dump (count($all));
		
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => $paginator,
				'sort_by' => $sort_by,
				'item_status' => $item_status,
				'per_pape' => $resultsPerPage,
				'item_type' => $item_type 
		) );
	}
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
	 */
	public function uploadPictureAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$user = $this->userTable->getUserByEmail ( $this->identity () );
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user ['id'] );
		
		if ($request->isPost ()) {
			
			$pictures = $_POST ['pictures'];
			$id = $_POST ['target_id'];
			
			$result = "";
			
			foreach ( $pictures as $p ) {
				$filetype = $p [0];
				$result = $result . $p [2];
				$original_filename = $p [2];
				
				if (preg_match ( '/(jpg|jpeg)$/', $filetype )) {
					$ext = 'jpg';
				} else if (preg_match ( '/(gif)$/', $filetype )) {
					$ext = 'gif';
				} else if (preg_match ( '/(png)$/', $filetype )) {
					$ext = 'png';
				}
				
				$tmp_name = md5 ( $id . uniqid ( microtime () ) ) . '.' . $ext;
				
				// remove "data:image/png;base64,"
				$uri = substr ( $p [1], strpos ( $p [1], "," ) + 1 );
				
				// save to file
				file_put_contents ( $tmp_name, base64_decode ( $uri ) );
				
				$checksum = md5_file ( $tmp_name );
				
				// $root_dir = $this->articleService->getPicturesPath ();
				$root_dir = ROOT . "/data/inventory/picture/item/";
				
				$criteria = array (
						"checksum" => $checksum,
						"item" => $id 
				);
				
				$ck = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPicture' )->findby ( $criteria );
				
				if (count ( $ck ) == 0) {
					$name = md5 ( $id . $checksum . uniqid ( microtime () ) ) . '.' . $ext;
					$folder = $root_dir . DIRECTORY_SEPARATOR . $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
					
					if (! is_dir ( $folder )) {
						mkdir ( $folder, 0777, true ); // important
					}
					
					rename ( $tmp_name, "$folder/$name" );
					
					try {
						$entity = new NmtInventoryItemPicture ();
						$entity->setUrl ( $folder . DIRECTORY_SEPARATOR . $name );
						$entity->setFiletype ( $filetype );
						$entity->setFilename ( $name );
						$entity->setOriginalFilename ( $original_filename );
						$entity->setFolder ( $folder );
						$entity->setChecksum ( $checksum );
						$item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $id );
						$entity->setItem ( $item );
						$entity->setCreatedBy ( $u );
						$entity->setCreatedOn ( new \DateTime () );
						
						$this->doctrineEM->persist ( $entity );
						$this->doctrineEM->flush ();
					} catch ( Exception $e ) {
						$result = $e->getMessage ();
					}
					
					// trigger uploadPicture. AbtractController is EventManagerAware.
					$this->getEventManager ()->trigger ( 'uploadPicture', __CLASS__, array (
							'picture_name' => $name,
							'pictures_dir' => $folder 
					) );
					
					$result = $result . ' uploaded. //';
				} else {
					$result = $result . ' exits. //';
				}
			}
			// $data['filetype'] = $filetype;
			$data = array ();
			$data ['message'] = $result;
			$response = $this->getResponse ();
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
			$response->setContent ( json_encode ( $data ) );
			return $response;
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		// $company = $this->articleTable->get ( $id );
		// $company = $this->doctrineEM->find('Application\Entity\NmtApplicationCompanyLogo',$id);
		$item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $id );
		
		return new ViewModel ( array (
				'item' => $item,
				'redirectUrl' => $redirectUrl,
				'errors' => null 
		) );
	}
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function getPictureAction() {
		/*
		 * $request = $this->getRequest ();
		 *
		 * // accepted only ajax request
		 * if (!$request->isXmlHttpRequest ()) {
		 * return $this->redirect ()->toRoute ( 'access_denied' );
		 * }
		 */
		$item_id = ( int ) $this->params ()->fromQuery ( 'item_id' );
		$pic1 = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPicture' )->findOneBy ( array (
				'item' => $item_id 
		) );
		
		if ($pic1 instanceof NmtInventoryItemPicture) {
			
			$pic = new NmtInventoryItemPicture ();
			$pic = $pic1;
			$pic_folder = getcwd () . "/data/inventory/picture/item/" . $pic->getFolderRelative () . "thumbnail_450_" . $pic->getFileName ();
			$imageContent = file_get_contents ( $pic_folder );
			
			$response = $this->getResponse ();
			
			$response->setContent ( $imageContent );
			$response->getHeaders ()->addHeaderLine ( 'Content-Transfer-Encoding', 'binary' )->addHeaderLine ( 'Content-Type', $pic->getFiletype () )->addHeaderLine ( 'Content-Length', mb_strlen ( $imageContent ) );
			return $response;
		} else {
			$pic_folder = getcwd () . "/public/images/no-pic.jpg";
			$imageContent = file_get_contents ( $pic_folder );
			
			$response = $this->getResponse ();
			
			$response->setContent ( $imageContent );
			$response->getHeaders ()->addHeaderLine ( 'Content-Transfer-Encoding', 'binary' )->addHeaderLine ( 'Content-Type', 'image/jpeg' )->addHeaderLine ( 'Content-Length', mb_strlen ( $imageContent ) );
			return $response;
		}
	}
	
	/**
	 */
	public function barcodeAction() {
		$barcode = ( int ) $this->params ()->fromQuery ( 'barcode' );
		
		// Only the text to draw is required
		$barcodeOptions = array (
				'text' => $barcode 
		);
		
		// No required options
		$rendererOptions = array ();
		
		// Draw the barcode in a new image,
		Barcode::factory ( 'code39', 'image', $barcodeOptions, $rendererOptions )->render ();
	}
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable($userTable) {
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
