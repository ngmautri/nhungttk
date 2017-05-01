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
use Application\Entity\NmtInventoryItemAttachment;
use Zend\Http\Headers;
use Zend\Validator\Date;
use Application\Entity\NmtInventoryItemPurchasing;

/*
 * Control Panel Controller
 */
class ItemPurchaseController extends AbstractActionController {
	protected $doctrineEM;
	protected $itemSearchService;
	protected $userTable;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		$message = $this->flashMessenger ()->getSuccessMessages ();
		$this->flashMessenger ()->clearCurrentMessages ();
		
		return new ViewModel ( array (
				'message' => $message 
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
		$user = $this->userTable->getUserByEmail ( $this->identity () );
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user ['id'] );
		
		if ($request->isPost ()) {
			$errors = array ();
			
			$new_entity_id = $request->getPost ( 'new_entity_id' );
			$item_id = $request->getPost ( 'item_id' );
			$item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $item_id );
			
			// create new if, no entity_id found
			if ($new_entity_id == null) {
				
				$entity = new NmtInventoryItemPurchasing ();
				
				if ($item == null) {
					$errors [] = 'Item not found!';
				} else {
					$entity->setItem ( $item );
				}
				
				$vendor_id = $request->getPost ( 'vendor_id' );
				$vendor = $this->doctrineEM->find ( 'Application\Entity\NmtBpVendor', $vendor_id );
				
				if ($vendor == null) {
					$errors [] = 'Vendor can\'t be empty. Please select a vendor!';
				} else {
					$entity->setVendor ( $vendor );
				}
				
				$is_preferred_vendor = $request->getPost ( 'is_preferred_vendor' );
				$entity->setIsPreferredVendor ( $is_preferred_vendor );
				
				$is_active = $request->getPost ( 'is_active' );
				if($is_active==null){
					$is_active=0;
				}
				$entity->setIsActive($is_active);
				
				$vendor_item_code = $request->getPost ( 'vendor_item_code' );
				$entity->setVendorItemCode ( $vendor_item_code );
				
				$vendor_item_unit = $request->getPost ( 'vendor_item_unit' );
				
				if ($vendor_item_unit == null) {
					$errors [] = 'Please enter unit of purchase';
				} else {
					$entity->setVendorItemUnit ( $vendor_item_unit );
				}
				
				$conversion_factor= $request->getPost ( 'conversion_factor' );
				if ($conversion_factor== null) {
					$errors [] = 'Please  enter conversion factor';
				} else {
					
					if (! is_numeric ( $conversion_factor)) {
						$errors [] = 'converstion_factor must be a number.';
					} else {
						if ($conversion_factor<= 0) {
							$errors [] = 'converstion_factor must be greater than 0!';
						}
						$entity->setConversionFactor ( $conversion_factor);
					}
				}
				
				$vendor_unit_price = $request->getPost ( 'vendor_unit_price' );
				
				if (! is_numeric ( $vendor_unit_price )) {
					$errors [] = 'Price is not valid. It must be a number.';
				} else {
					if ($vendor_unit_price <= 0) {
						$errors [] = 'Price must be greate than 0!';
					}
					$entity->setVendorUnitPrice ( $vendor_unit_price );
				}
				
				$currency_id = $request->getPost ( 'currency_id' );
				$currency = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCurrency', $currency_id );
				
				if ($currency == null) {
					$errors [] = 'Curency can\'t be empty. Please select a currency!';
				} else {
					$entity->setCurrency ( $currency );
				}
				
				$price_valid_from = $request->getPost ( 'price_valid_from' );
				$price_valid_to = $request->getPost ( 'price_valid_to' );
				
				$date_validated = 0;
				$validator = new Date ();
				if (! $validator->isValid ( $price_valid_from )) {
					$errors [] = 'Price valid  date is not correct or empty!';
				} else {
					$entity->setPriceValidFrom ( new \DateTime ( $price_valid_from ) );
					$date_validated ++;
				}
				
				if ($price_valid_to !== "") {
					if (! $validator->isValid ( $price_valid_to )) {
						$errors [] = 'Price valid  to is not correct or empty!';
					} else {
						$entity->setPriceValidTo ( new \DateTime ( $price_valid_to ) );
						$date_validated ++;
					}
					
					if ($date_validated == 2) {
						
						if ($price_valid_from > $price_valid_to) {
							$errors [] = 'To date must > from date!';
						}
					}
				}
				
				$lead_time = $request->getPost ( 'lead_time' );
				$entity->setLeadTime ( $lead_time );
				
				$pmt_method_id = $request->getPost ( 'pmt_method_id' );
				$pmt_method = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationPmtMethod', $pmt_method_id );
				if (! $pmt_method == null) {
					$entity->setPmtMethod ( $pmt_method );
				}
				
				$remarks = $request->getPost ( 'remarks' );
				$entity->setRemarks ( $remarks );
				
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				if (count ( $errors ) > 0) {
					
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'errors' => $errors,
							'item' => $item,
							'entity' => $entity,
							'vendor' => $vendor,
							'currency' => $currency,
							'pmt_method' => $pmt_method,
							'new_entity_id' => null 
					
					) );
				}
				;
				
				// create purchase first
				$entity->setConversionText ( $entity->getVendorItemUnit () . ' = ' . $entity->getConversionFactor () . '*' . $item->getStandardUom ()->getUomCode () );
				$entity->setCreatedBy ( $u );
				$entity->setCreatedOn ( new \DateTime () );
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				$new_entity_id = $entity->getId();
			}
			
			$new_entity =  new NmtInventoryItemPurchasing();
			$new_entity_tmp = $pmt_method = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItemPurchasing', $new_entity_id );
			$new_entity = $new_entity_tmp;
			
			// adding attachment
			if ($new_entity !== null) {
				
				if (isset ( $_FILES ['attachments'] )) {
					$file_name = $_FILES ['attachments'] ['name'];
					$file_size = $_FILES ['attachments'] ['size'];
					$file_tmp = $_FILES ['attachments'] ['tmp_name'];
					$file_type = $_FILES ['attachments'] ['type'];
					$file_ext = strtolower ( end ( explode ( '.', $_FILES ['attachments'] ['name'] ) ) );
					echo ($file_name);
					
					// NOT empty attachment
					if ($file_tmp !== "" and $file_size < 2097152) {
						$ext = '';
						if (preg_match ( '/(jpg|jpeg)$/', $file_type )) {
							$ext = 'jpg';
						} else if (preg_match ( '/(gif)$/', $file_type )) {
							$ext = 'gif';
						} else if (preg_match ( '/(png)$/', $file_type )) {
							$ext = 'png';
						} else if (preg_match ( '/(pdf)$/', $file_type )) {
							$ext = 'pdf';
						} else if (preg_match ( '/(vnd.ms-excel)$/', $file_type )) {
							$ext = 'xls';
						} else if (preg_match ( '/(vnd.openxmlformats-officedocument.spreadsheetml.sheet)$/', $file_type )) {
							$ext = 'xlsx';
						} else if (preg_match ( '/(msword)$/', $file_type )) {
							$ext = 'doc';
						} else if (preg_match ( '/(vnd.openxmlformats-officedocument.wordprocessingml.document)$/', $file_type )) {
							$ext = 'docx';
						} else if (preg_match ( '/(x-zip-compressed)$/', $file_type )) {
							$ext = 'zip';
						} else if (preg_match ( '/(octet-stream)$/', $file_type )) {
							$ext = $file_ext;
						}
						
						$expensions = array (
								"jpeg",
								"jpg",
								"png",
								"pdf",
								"xlsx",
								"xls",
								"docx",
								"doc",
								"zip",
								"msg" 
						);
						
						$errors = array ();
						
						if (in_array ( $ext, $expensions ) === false) {
							$errors [] = 'Extension file"' . $ext . '" not supported, please choose a "jpeg",
							"jpg",
							"png",
							"pdf",
							"xlsx",
							"xls",
							"docx",
							"doc",
							"zip",
							"msg" !';
						}
						
						if ($file_size > 2097152) {
							$errors [] = 'File size must be excately 2 MB';
						}
						
						$checksum = md5_file ( $file_tmp );
						$criteria = array (
								"checksum" => $checksum,
								"item" => $item_id 
						);
						$ck = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemAttachment' )->findby ( $criteria );
						
						if (count ( $ck ) > 0) {
							// update attachment with new_entity_id;
						}
						
						if ($item == null) {
							$errors [] = 'Item not found!';
						}
						
						if (count ( $errors ) > 0) {
							
							return new ViewModel ( array (
									'redirectUrl' => $redirectUrl,
									'errors' => $errors,
									'item' => $item,
									'entity' => $new_entity,
									'vendor' => $new_entity->getVendor(),
									'currency' => $new_entity->getCurrency(),
									'pmt_method' => $new_entity->getPmtMethod(),
									'new_entity_id' => $new_entity_id 
							) );
						}
						;
						
						$name = md5 ( $item_id . $checksum . uniqid ( microtime () ) ) . '_' . $this->generateRandomString () . '_' . $this->generateRandomString ( 10 ) . '.' . $ext;
						
						$root_dir = ROOT . "/data/inventory/attachment/item";
						$folder_relative = $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
						$folder = $root_dir . DIRECTORY_SEPARATOR . $folder_relative;
						
						if (! is_dir ( $folder )) {
							mkdir ( $folder, 0777, true ); // important
						}
						
						// echo ("$folder/$name");
						
						move_uploaded_file ( $file_tmp, "$folder/$name" );
						
						$pdf_box = ROOT . "/vendor/pdfbox/";
						
						// java -jar pdfbox-app-2.0.5.jar Encrypt [OPTIONS] <password> <inputfile>
						exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar Encrypt -O mla2017 -U mla2017 ' . "$folder/$name" );
						
						// extract text:
						exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar ExtractText -password mla2017 ' . "$folder/$name" . ' ' . "$folder/$name" . '.txt' );
						
						// echo('java -jar ' . $pdf_box.'/pdfbox-app-2.0.5.jar Encrypt -O nmt -U nmt '."$folder/$name");
						
						// update database
						$entity = new NmtInventoryItemAttachment ();
						$entity->setDocumentType ( "Attachment Puchasing: ". $new_entity->getID());
						
						//$entity->setDocumentType ( "Purchasing attachment: " . $new_entity->getItem()->getID());
						$entity->setFilename ( $name );
						$entity->setFiletype ( $file_type );
						$entity->setFilenameOriginal ( $file_name );
						$entity->setSize ( $file_size );
						$entity->setFolder ( $folder );
						$entity->setFolderRelative ( $folder_relative . DIRECTORY_SEPARATOR );
						$entity->setChecksum ( $checksum );
						
						$entity->setItem ( $item );
						$entity->setVendor ( $new_entity->getVendor());
						$entity->setItemPurchasing($new_entity);
						
						$entity->setCreatedBy ( $u );
						$entity->setCreatedOn ( new \DateTime () );
						$this->doctrineEM->persist ( $entity );
						$this->doctrineEM->flush ();
						//$new_attachement_id = $entity->getId();
						
						
						return $this->redirect ()->toUrl ( $redirectUrl );
					}
				}
			}
			
			return $this->redirect ()->toUrl ( $redirectUrl );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		$item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $id );
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'item' => $item,
				'entity' => null,
				'vendor' => null,
				'currency' => null,
				'pmt_method' => null,
				'new_entity_id' => null 
		
		) );
	}
	
	/**
	 *
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function showAction() {
		$request = $this->getRequest ();
		
		if ($request->getHeader ( 'Referer' ) == null) {
		 return $this->redirect ()->toRoute ( 'access_denied' );
		 }
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$user = $this->userTable->getUserByEmail ( $this->identity () );
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user ['id'] );
		
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		$item_purchasing = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItemPurchasing', $id );
		
		$vendor=null;
		$currency=null;
		$pmt_method=null;
		$item=null;
		if($item_purchasing!==null){
			$vendor=$item_purchasing->getVendor();
			$currency=$item_purchasing->getCurrency();
			$pmt_method=$item_purchasing->getPmtMethod();
			$item=$item_purchasing->getItem();
		}
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'item' => $item,
				'entity' => $item_purchasing,
				'vendor' => $vendor,
				'currency' => $currency,
				'pmt_method' => $pmt_method,
				'new_entity_id' => null
				
		) );
	}
	/**
	 *
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function editAction() {
		$request = $this->getRequest ();
		
		/* if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		} */
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$user = $this->userTable->getUserByEmail ( $this->identity () );
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user ['id'] );
		
		if ($request->isPost ()) {
			$errors = array ();
			
			$entity_id = $request->getPost ( 'entity_id' );
			//$item_id = $request->getPost ( 'item_id' );
			$entity= $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItemPurchasing', $entity_id);
			//echo($entity->getVendorItemCode());
			
			// create new if, no entity_id found
			if ($entity !== null) {
		
				// need commented
				//$entity = new NmtInventoryItemPurchasing ();
				
				$item = $entity->getItem();
				
				$item_id=null;
				if($item!==null){
					$item_id =  $item->getId();
				}
												
				$vendor_id = $request->getPost ( 'vendor_id' );
				$vendor = $this->doctrineEM->find ( 'Application\Entity\NmtBpVendor', $vendor_id );
				
				if ($vendor == null) {
					$errors [] = 'Vendor can\'t be empty. Please select a vendor!';
				} else {
					$entity->setVendor ( $vendor );
				}
				
				$is_preferred_vendor = $request->getPost ( 'is_preferred_vendor' );
				$entity->setIsPreferredVendor ( $is_preferred_vendor );
				
				$is_active = $request->getPost ( 'is_active' );
				if($is_active==null){
					$is_active=0;
				}
				$entity->setIsActive($is_active);
				
				$vendor_item_code = $request->getPost ( 'vendor_item_code' );
				$entity->setVendorItemCode ( $vendor_item_code );
				
				$vendor_item_unit = $request->getPost ( 'vendor_item_unit' );
				
				if ($vendor_item_unit == null) {
					$errors [] = 'Please enter unit of purchase';
				} else {
					$entity->setVendorItemUnit ( $vendor_item_unit );
				}
				
				$conversion_factor= $request->getPost ( 'conversion_factor' );
				if ($conversion_factor== null) {
					$errors [] = 'Please  enter conversion factor';
				} else {
					
					if (! is_numeric ( $conversion_factor)) {
						$errors [] = 'converstion_factor must be a number.';
					} else {
						if ($conversion_factor<= 0) {
							$errors [] = 'converstion_factor must be greater than 0!';
						}
						$entity->setConversionFactor ( $conversion_factor);
					}
				}
				
				$vendor_unit_price = $request->getPost ( 'vendor_unit_price' );
				
				if (! is_numeric ( $vendor_unit_price )) {
					$errors [] = 'Price is not valid. It must be a number.';
				} else {
					if ($vendor_unit_price <= 0) {
						$errors [] = 'Price must be greate than 0!';
					}
					$entity->setVendorUnitPrice ( $vendor_unit_price );
				}
				
				$currency_id = $request->getPost ( 'currency_id' );
				$currency = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCurrency', $currency_id );
				
				if ($currency == null) {
					$errors [] = 'Curency can\'t be empty. Please select a currency!';
				} else {
					$entity->setCurrency ( $currency );
				}
				
				$price_valid_from = $request->getPost ( 'price_valid_from' );
				$price_valid_to = $request->getPost ( 'price_valid_to' );
				
				$date_validated = 0;
				$validator = new Date ();
				if (! $validator->isValid ( $price_valid_from )) {
					$errors [] = 'Price valid  date is not correct or empty!';
				} else {
					$entity->setPriceValidFrom ( new \DateTime ( $price_valid_from ) );
					$date_validated ++;
				}
				
				if ($price_valid_to !== "") {
					if (! $validator->isValid ( $price_valid_to )) {
						$errors [] = 'Price valid  to is not correct or empty!';
					} else {
						$entity->setPriceValidTo ( new \DateTime ( $price_valid_to ) );
						$date_validated ++;
					}
					
					if ($date_validated == 2) {
						
						if ($price_valid_from > $price_valid_to) {
							$errors [] = 'To date must > from date!';
						}
					}
				}
				
				$lead_time = $request->getPost ( 'lead_time' );
				$entity->setLeadTime ( $lead_time );
				
				$pmt_method_id = $request->getPost ( 'pmt_method_id' );
				$pmt_method = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationPmtMethod', $pmt_method_id );
				if (! $pmt_method == null) {
					$entity->setPmtMethod ( $pmt_method );
				}
				
				$remarks = $request->getPost ( 'remarks' );
				$entity->setRemarks ( $remarks );
				
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				if (count ( $errors ) > 0) {
					
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'errors' => $errors,
							'item' => $item,
							'entity' => $entity,
							'vendor' => $vendor,
							'currency' => $currency,
							'pmt_method' => $pmt_method,
							'new_entity_id' => null
							
					) );
				}
				;
				
				// create purchase first
				$entity->setConversionText ( $entity->getVendorItemUnit () . ' = ' . $entity->getConversionFactor () . '*' . $item->getStandardUom ()->getUomCode () );
				
				$entity->setLastChangeOn ( new \DateTime () );
				$entity->setLastChangeBy( $u );
				$this->doctrineEM->flush ();
				//$new_entity_id = $entity->getId();
			}
			
			/* $new_entity =  new NmtInventoryItemPurchasing();
			$new_entity_tmp = $pmt_method = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItemPurchasing', $new_entity_id );
			$new_entity = $new_entity_tmp;
			 */
			
			$new_entity = $entity;
			
			// adding attachment
			if ($new_entity !== null) {
				
				if (isset ( $_FILES ['attachments'] )) {
					$file_name = $_FILES ['attachments'] ['name'];
					$file_size = $_FILES ['attachments'] ['size'];
					$file_tmp = $_FILES ['attachments'] ['tmp_name'];
					$file_type = $_FILES ['attachments'] ['type'];
					$file_ext = strtolower ( end ( explode ( '.', $_FILES ['attachments'] ['name'] ) ) );
					echo ($file_name);
					
					// NOT empty attachment
					if ($file_tmp !== "" and $file_size < 2097152) {
						$ext = '';
						if (preg_match ( '/(jpg|jpeg)$/', $file_type )) {
							$ext = 'jpg';
						} else if (preg_match ( '/(gif)$/', $file_type )) {
							$ext = 'gif';
						} else if (preg_match ( '/(png)$/', $file_type )) {
							$ext = 'png';
						} else if (preg_match ( '/(pdf)$/', $file_type )) {
							$ext = 'pdf';
						} else if (preg_match ( '/(vnd.ms-excel)$/', $file_type )) {
							$ext = 'xls';
						} else if (preg_match ( '/(vnd.openxmlformats-officedocument.spreadsheetml.sheet)$/', $file_type )) {
							$ext = 'xlsx';
						} else if (preg_match ( '/(msword)$/', $file_type )) {
							$ext = 'doc';
						} else if (preg_match ( '/(vnd.openxmlformats-officedocument.wordprocessingml.document)$/', $file_type )) {
							$ext = 'docx';
						} else if (preg_match ( '/(x-zip-compressed)$/', $file_type )) {
							$ext = 'zip';
						} else if (preg_match ( '/(octet-stream)$/', $file_type )) {
							$ext = $file_ext;
						}
						
						$expensions = array (
								"jpeg",
								"jpg",
								"png",
								"pdf",
								"xlsx",
								"xls",
								"docx",
								"doc",
								"zip",
								"msg"
						);
						
						$errors = array ();
						
						if (in_array ( $ext, $expensions ) === false) {
							$errors [] = 'Extension file"' . $ext . '" not supported, please choose a "jpeg",
							"jpg",
							"png",
							"pdf",
							"xlsx",
							"xls",
							"docx",
							"doc",
							"zip",
							"msg" !';
						}
						
						if ($file_size > 2097152) {
							$errors [] = 'File size must be excately 2 MB';
						}
						
						$checksum = md5_file ( $file_tmp );
						$criteria = array (
								"checksum" => $checksum,
								"item" => $item_id
						);
						$ck = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemAttachment' )->findby ( $criteria );
						
						if (count ( $ck ) > 0) {
							// update attachment with new_entity_id;
						}
						
						if ($item == null) {
							$errors [] = 'Item not found!';
						}
						
						if (count ( $errors ) > 0) {
							
							return new ViewModel ( array (
									'redirectUrl' => $redirectUrl,
									'errors' => $errors,
									'item' => $item,
									'entity' => $new_entity,
									'vendor' => $new_entity->getVendor(),
									'currency' => $new_entity->getCurrency(),
									'pmt_method' => $new_entity->getPmtMethod(),
									'new_entity_id' => $new_entity_id
							) );
						}
						;
						
						$name = md5 ( $item_id . $checksum . uniqid ( microtime () ) ) . '_' . $this->generateRandomString () . '_' . $this->generateRandomString ( 10 ) . '.' . $ext;
						
						$root_dir = ROOT . "/data/inventory/attachment/item";
						$folder_relative = $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
						$folder = $root_dir . DIRECTORY_SEPARATOR . $folder_relative;
						
						if (! is_dir ( $folder )) {
							mkdir ( $folder, 0777, true ); // important
						}
						
						// echo ("$folder/$name");
						
						move_uploaded_file ( $file_tmp, "$folder/$name" );
						
						$pdf_box = ROOT . "/vendor/pdfbox/";
						
						// java -jar pdfbox-app-2.0.5.jar Encrypt [OPTIONS] <password> <inputfile>
						exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar Encrypt -O mla2017 -U mla2017 ' . "$folder/$name" );
						
						// extract text:
						exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar ExtractText -password mla2017 ' . "$folder/$name" . ' ' . "$folder/$name" . '.txt' );
						
						// echo('java -jar ' . $pdf_box.'/pdfbox-app-2.0.5.jar Encrypt -O nmt -U nmt '."$folder/$name");
						
						// update database
						$entity = new NmtInventoryItemAttachment ();
						$entity->setDocumentType ( "Attachment Puchasing: ". $new_entity->getID());
						
						//$entity->setDocumentType ( "Purchasing attachment: " . $new_entity->getItem()->getID());
						$entity->setFilename ( $name );
						$entity->setFiletype ( $file_type );
						$entity->setFilenameOriginal ( $file_name );
						$entity->setSize ( $file_size );
						$entity->setFolder ( $folder );
						$entity->setFolderRelative ( $folder_relative . DIRECTORY_SEPARATOR );
						$entity->setChecksum ( $checksum );
						
						$entity->setItem ( $item );
						$entity->setVendor ( $new_entity->getVendor());
						$entity->setItemPurchasing($new_entity);
						
						$entity->setCreatedBy ( $u );
						$entity->setCreatedOn ( new \DateTime () );
						$this->doctrineEM->persist ( $entity );
						$this->doctrineEM->flush ();
						//$new_attachement_id = $entity->getId();
						
						
						//return $this->redirect ()->toUrl ( $redirectUrl );
					}
				}
			}
			//echo("dsfdfs");
			return $this->redirect ()->toUrl ( $redirectUrl );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		$item_purchasing = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItemPurchasing', $id );
		
		$vendor=null;
		$currency=null;
		$pmt_method=null;
		$item=null;
		
		if($item_purchasing!==null){
			$vendor=$item_purchasing->getVendor();
			$currency=$item_purchasing->getCurrency();
			$pmt_method=$item_purchasing->getPmtMethod();
			$item=$item_purchasing->getItem();
		}
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'item' => $item,
				'entity' => $item_purchasing,
				'vendor' => $vendor,
				'currency' => $currency,
				'pmt_method' => $pmt_method,
				'new_entity_id' => null
				
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
		
		$criteria = array (
				
		);
		
		// var_dump($criteria);
		
		$sort_criteria = array ();
		
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
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPurchasing' )->findBy ( $criteria, $sort_criteria );
		$total_records = count ( $list );
		$paginator = null;
		
		if ($total_records > $resultsPerPage) {
			$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPurchasing' )->findBy ( $criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		// $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
		// var_dump (count($all));
		
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => $paginator 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function list1Action() {
		$request = $this->getRequest ();
		
		// accepted only ajax request
		/*
		 * if (! $request->isXmlHttpRequest ()) {
		 * return $this->redirect ()->toRoute ( 'access_denied' );
		 * }
		 * ;
		 */
		
		$this->layout ( "layout/user/ajax" );
		
		$id = ( int ) $this->params ()->fromQuery ( 'item_id' );
		// $item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $id );
		
		$criteria = array (
				'item' => $id 
		);
		
		$sort_criteria = array (
				'priceValidFrom' => "DESC"
		);
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPurchasing' )->findBy ( $criteria,$sort_criteria);
		$total_records = count ( $list );
		$paginator = null;
		
		// $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
		// var_dump (count($all));
		
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => $paginator 
		) );
	}
	
	/**
	 *
	 * @return \Doctrine\ORM\EntityManager
	 */
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
	
	/**
	 *
	 * @param number $length        	
	 * @return string
	 */
	private function generateRandomString($length = 6) {
		return substr ( str_shuffle ( str_repeat ( $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil ( $length / strlen ( $x ) ) ) ), 1, $length );
	}
}
