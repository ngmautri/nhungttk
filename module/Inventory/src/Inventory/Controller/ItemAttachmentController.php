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

/*
 * Control Panel Controller
 */
class ItemAttachmentController extends AbstractActionController {
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
	public function showAction() {
		$request = $this->getRequest ();
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$redirectUrl = $request->getHeader ( 'Referer' )->getUri ();
		
		$target_id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		$entity = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItemAttachment', $target_id );
		
		$item = null;
		$vendor = null;
		if (! $entity == null) {
			$item = $entity->getItem ();
			$vendor = $entity->getVendor ();
		}
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'item' => $item,
				'entity' => $entity,
				'vendor' => $vendor 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addAction() {
	}
	
	/**
	 *
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function editAction() {
		$request = $this->getRequest ();
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$user = $this->userTable->getUserByEmail ( $this->identity () );
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user ['id'] );
		
		if ($request->isPost ()) {
			
			$errors = array ();
			
			$entity_id = $request->getPost ( 'entity_id' );
			$entity = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItemAttachment', $entity_id );
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			
			if ($entity !== null) {
				$vendor_id = $request->getPost ( 'vendor_id' );
				$is_active = $request->getPost ( 'is_active' );
				if ($is_active == null) {
					$is_active = 0;
				}
				$document_type = $request->getPost ( 'document_type' );
				$remarks = $request->getPost ( 'remarks' );
				
				// $entity = new NmtInventoryItemAttachment ();
				
				$item = $entity->getItem ();
				$item_id = null;
				if ($item !== null) {
					$item_id = $item->getId ();
				}
				$entity->setDocumentType ( $document_type );
				$entity->setIsActive ( $is_active );
				$entity->setRemarks ( $remarks );
				
				if ($document_type == null) {
					$errors [] = 'Please give document type';
				}
				
				$vendor = null;
				if ($vendor_id > 0) {
					$vendor = $this->doctrineEM->find ( 'Application\Entity\NmtBpVendor', $vendor_id );
				}	
				$entity->setVendor ( $vendor ); // Optional
				
				if (isset ( $_FILES ['attachments'] )) {
					$file_name = $_FILES ['attachments'] ['name'];
					$file_size = $_FILES ['attachments'] ['size'];
					$file_tmp = $_FILES ['attachments'] ['tmp_name'];
					$file_type = $_FILES ['attachments'] ['type'];
					$file_ext = strtolower ( end ( explode ( '.', $_FILES ['attachments'] ['name'] ) ) );
					
					if ($file_tmp !== "") {
						
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
						
						if (in_array ( $ext, $expensions ) === false) {
							$errors [] = 'Extension file"' . $ext . '" not supported, please choose a "jpeg","jpg","png","pdf","xlsx","xlx", "docx"!';
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
							$errors [] = 'Document: "' . $file_name . '"  exits already';
						}
						
						if (count ( $errors ) > 0) {
							
							return new ViewModel ( array (
									'redirectUrl' => $redirectUrl,
									'errors' => $errors,
									'item' => $item,
									'entity' => $entity,
									'vendor' => $vendor 
							
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
						/* $entity->setFilename ( $name );
						$entity->setFiletype ( $file_type );
						$entity->setFilenameOriginal ( $file_name );
						$entity->setSize ( $file_size );
						$entity->setFolder ( $folder );
						$entity->setFolderRelative ( $folder_relative . DIRECTORY_SEPARATOR );
						$entity->setChecksum ( $checksum );
						
						$entity->setCreatedBy ( $u );
						$entity->setCreatedOn ( new \DateTime () ); */
						$entity->setIsactive(0);
						
						$cloned_entity = clone $entity;
						
						$cloned_entity->setIsactive(1);
						$cloned_entity->setFilename ( $name );
						$cloned_entity->setFiletype ( $file_type );
						$cloned_entity->setFilenameOriginal ( $file_name );
						$cloned_entity->setSize ( $file_size );
						$cloned_entity->setFolder ( $folder );
						$cloned_entity->setFolderRelative ( $folder_relative . DIRECTORY_SEPARATOR );
						$cloned_entity->setChecksum ( $checksum );
						$cloned_entity->setCreatedBy ( $u );
						$cloned_entity->setCreatedOn ( new \DateTime () );
						$cloned_entity->setRemarks("cloned");
						$this->doctrineEM->persist ( $cloned_entity );
						$this->doctrineEM->flush ();
						
						return $this->redirect ()->toUrl ( $redirectUrl );
					}
				}
				
				$entity->setLastChangeBy ( $u );
				$entity->setLastChangeOn ( new \DateTime () );
				$this->doctrineEM->flush ();
				return $this->redirect ()->toUrl ( $redirectUrl );
			}
			
			$errors [] = 'Item can\'t be found!';
			return new ViewModel ( array (
					'redirectUrl' => $redirectUrl,
					'errors' => $errors,
					'item' => null,
					'entity' => null,
					'vendor' => null 
			) );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		$entity = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItemAttachment', $id );
		
		$item = null;
		$vendor = null;
		if (! $entity == null) {
			$item = $entity->getItem ();
			$vendor = $entity->getVendor ();
		}
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'item' => $item,
				'entity' => $entity,
				'vendor' => $vendor 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
		$criteria = array ();
		// var_dump($criteria);
		
		$sort_criteria = array ();
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 10;
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
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemAttachment' )->findBy ( $criteria, $sort_criteria );
		$total_records = count ( $list );
		$paginator = null;
		
		if ($total_records > $resultsPerPage) {
			$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemAttachment' )->findBy ( $criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
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
		if (! $request->isXmlHttpRequest ()) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		;
		
		$this->layout ( "layout/user/ajax" );
		
		$id = ( int ) $this->params ()->fromQuery ( 'item_id' );
		// $item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $id );
		
		$criteria = array (
				'item' => $id 
		);
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemAttachment' )->findBy ( $criteria );
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
	 * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
	 */
	public function uploadAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$user = $this->userTable->getUserByEmail ( $this->identity () );
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user ['id'] );
		
		if ($request->isPost ()) {
			
			$errors = array ();
			
			$item_id = $request->getPost ( 'item_id' );
			$item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $item_id );
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			
			if ($item !== null) {
				
				$vendor_id = $request->getPost ( 'vendor_id' );
				$is_active = $request->getPost ( 'is_active' );
				if ($is_active == null) {
					$is_active = 0;
				}
				$document_type = $request->getPost ( 'document_type' );
				$remarks = $request->getPost ( 'remarks' );
				
				$entity = new NmtInventoryItemAttachment ();
				$entity->setDocumentType ( $document_type );
				$entity->setIsActive ( $is_active );
				$entity->setItem ( $item );
				$entity->setRemarks ( $remarks );
				
				if ($document_type == null) {
					$errors [] = 'Please give document type';
				}
				
				$vendor = null;
				if ($vendor_id > 0) {
					$vendor = $this->doctrineEM->find ( 'Application\Entity\NmtBpVendor', $vendor_id );
					$entity->setVendor ( $vendor );
				}
				
				if (isset ( $_FILES ['attachments'] )) {
					$file_name = $_FILES ['attachments'] ['name'];
					$file_size = $_FILES ['attachments'] ['size'];
					$file_tmp = $_FILES ['attachments'] ['tmp_name'];
					$file_type = $_FILES ['attachments'] ['type'];
					$file_ext = strtolower ( end ( explode ( '.', $_FILES ['attachments'] ['name'] ) ) );
					
					if ($file_tmp !== "") {
						
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
						
						if (in_array ( $ext, $expensions ) === false) {
							$errors [] = 'Extension file"' . $ext . '" not supported, please choose a "jpeg","jpg","png","pdf","xlsx","xlx", "docx"!';
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
							$errors [] = 'Document: "' . $file_name . '"  exits already';
						}
						
						if (count ( $errors ) > 0) {
							
							return new ViewModel ( array (
									'redirectUrl' => $redirectUrl,
									'errors' => $errors,
									'item' => $item,
									'entity' => $entity,
									'vendor' => $vendor 
							
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
						$entity->setFilename ( $name );
						$entity->setFiletype ( $file_type );
						$entity->setFilenameOriginal ( $file_name );
						$entity->setSize ( $file_size );
						$entity->setFolder ( $folder );
						$entity->setFolderRelative ( $folder_relative . DIRECTORY_SEPARATOR );
						$entity->setChecksum ( $checksum );
						
						$entity->setCreatedBy ( $u );
						$entity->setCreatedOn ( new \DateTime () );
						$this->doctrineEM->persist ( $entity );
						$this->doctrineEM->flush ();
						
						return $this->redirect ()->toUrl ( $redirectUrl );
					}
				}
				
				$errors [] = 'Attachment can\'t be empby!';
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'errors' => $errors,
						'item' => $item,
						'entity' => $entity,
						'vendor' => $vendor 
				) );
			}
			
			$errors [] = 'Item can\'t be found!';
			return new ViewModel ( array (
					'redirectUrl' => $redirectUrl,
					'errors' => $errors,
					'item' => $item,
					'entity' => null,
					'vendor' => null 
			) );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		$item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $id );
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'item' => $item,
				'entity' => null,
				'vendor' => null 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function downloadAction() {
		
		$request = $this->getRequest ();
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		// $request = $this->getRequest ();
		$root_dir = ROOT . "/data/inventory/attachment/item/";
		$target_id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		
		$item_attachment = new NmtInventoryItemAttachment ();
		$tmp_attachment = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItemAttachment', $target_id );
		$item_attachment = $tmp_attachment;
		
		$f = $root_dir . $item_attachment->getFolderRelative () . $item_attachment->getFilename ();
		// echo($f);
		// fseek ( $fh, 0 );
		// $output = stream_get_contents ( $f);
		// file_put_contents($fileName, $output);
		
		$output = file_get_contents ( $f );
		
		$response = $this->getResponse ();
		$headers = new Headers ();
		
		$headers->addHeaderLine ( 'Content-Type: ' . $item_attachment->getFiletype () );
		$headers->addHeaderLine ( 'Content-Disposition: attachment; filename="' . $item_attachment->getFilenameOriginal () . '"' );
		$headers->addHeaderLine ( 'Content-Description: File Transfer' );
		$headers->addHeaderLine ( 'Content-Transfer-Encoding: binary' );
		$headers->addHeaderLine ( 'Content-Encoding: UTF-8' );
		
		$response->setHeaders ( $headers );
		
		$response->setContent ( $output );
		
		// fclose ( $fh );
		// unlink($fileName);
		return $response;
		
		/*
		 * return new ViewModel ( array (
		 *
		 * ) );
		 */
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function uploadPdfAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$user = $this->userTable->getUserByEmail ( $this->identity () );
		$u = $this->doctrineEM->find ( 'Application\Entity\MlaUsers', $user ['id'] );
		
		if ($request->isPost ()) {
			
			$item_id = $request->getPost ( 'item_id' );
			$item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $item_id );
			
			if (isset ( $_FILES ['attachments'] )) {
				$errors = array ();
				$file_name = $_FILES ['attachments'] ['name'];
				$file_size = $_FILES ['attachments'] ['size'];
				$file_tmp = $_FILES ['attachments'] ['tmp_name'];
				$file_type = $_FILES ['attachments'] ['type'];
				// $file_ext = strtolower ( end ( explode ( '.', $_FILES ['attachments'] ['name'] ) ) );
				
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
				}
				
				var_dump ( $file_name );
				var_dump ( $file_size );
				var_dump ( $file_tmp );
				var_dump ( $file_type );
				var_dump ( $ext );
				
				/*
				 * $expensions = array (
				 * "jpeg",
				 * "jpg",
				 * "png"
				 * );
				 *
				 * if (in_array ( $file_ext, $expensions ) === false) {
				 * $errors [] = "extension not allowed, please choose a JPEG or PNG file.";
				 * }
				 *
				 * if ($file_size > 2097152) {
				 * $errors [] = 'File size must be excately 2 MB';
				 * }
				 *
				 * if (empty ( $errors ) == true) {
				 * move_uploaded_file ( $file_tmp, "images/" . $file_name );
				 * echo "Success";
				 * } else {
				 * print_r ( $errors );
				 * }
				 */
			}
			
			return new ViewModel ( array (
					'item' => $item,
					'redirectUrl' => $redirectUrl,
					'errors' => null,
					'attachment' => null 
			) );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		$item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $id );
		
		return new ViewModel ( array (
				'item' => $item,
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'attachment' => null 
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
