<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace PM\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use Application\Entity\NmtApplicationAttachment;
use Zend\Http\Headers;
use Zend\Validator\Date;

/*
 * Control Panel Controller
 */
class ProjectAttachmentController extends AbstractActionController {
	
	// to Change;
	const ATTACHMENT_FOLDER = "/data/pm/attachment/project";
	const PDFBOX_FOLDER = "/vendor/pdfbox/";
	const PDF_PASSWORD = "mla2017";
	protected $doctrineEM;
	
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
		
		$entity_id = ( int ) $this->params ()->fromQuery ( 'entity_id' );
		
		$entity = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationAttachment', $entity_id );
		
		// Target: PROJECT
		$target = null;
		if (! $entity == null) {
			$target = $entity->getProject ();
		}
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'target' => $target,
				'entity' => $entity 
		) );
	}
	

	
	/**
	 *
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function editAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
				"email" => $this->identity () 
		) );
		
		if ($request->isPost ()) {
			
			$errors = array ();
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			
			$entity_id = $request->getPost ( 'entity_id' );
			
			// Target: Project
			$entity = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationAttachment', $entity_id );
			
			if ($entity == null) {
				
				$errors [] = 'Entity object can\'t be empty!';
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'errors' => $errors,
						'target' => null,
						'entity' => null 
				) );
				
				// might need redirect
			} else {
				
				$vendor_id = $request->getPost ( 'vendor_id' );
				$documentSubject = $request->getPost ( 'documentSubject' );
				$validFrom = $request->getPost ( 'validFrom' );
				$validTo = $request->getPost ( 'validTo' );
				$isActive = $request->getPost ( 'isActive' );
				$markedForDeletion = $request->getPost ( 'markedForDeletion' );
				$filePassword = $request->getPost ( 'filePassword' );
				$visibility = $request->getPost ( 'visibility' );
				$filePassword = $request->getPost ( 'filePassword' );
				
				// Target: Project
				$target = $entity->getProject ();
				
				// to Add
				$target_id = null;
				if ($target !== null) {
					$target_id = $target->getId ();
				}
				
				// to Comment
				// $entity = new NmtApplicationAttachment ();
				
				// Target: PROJECT
				$entity->setProject ( $target );
				$entity->setFilePassword ( $filePassword );
				
				$remarks = $request->getPost ( 'remarks' );
				
				if ($documentSubject == null) {
					$errors [] = 'Please give document subject!';
				} else {
					$entity->setDocumentSubject ( $documentSubject );
				}
				
				if ($isActive !=1) {
					$isActive = 0;
				}
				
				if ($markedForDeletion !=1) {
					$markedForDeletion = 0;
				}
				
				if ($visibility !=1) {
					$visibility = 0;
				}
				
				if ($filePassword === null or $filePassword == "") {
					$filePassword = self::PDF_PASSWORD;
				}
				
				$entity->setIsActive ( $isActive );
				$entity->setMarkedForDeletion ( $markedForDeletion );
				$entity->setVisibility ( $visibility );
				
				// validator.
				$validator = new Date ();
				$date_to_validate = 2;
				$date_validated = 0;
				
				// EMPTY is ok
				if ($validFrom !== null) {
					if ($validFrom !== "") {
						if (! $validator->isValid ( $validFrom )) {
							$errors [] = 'Start date is not correct or empty!';
						} else {
							$date_validated ++;
							$entity->setValidFrom ( new \DateTime ( $validFrom ) );
						}
					}
				}
				
				// EMPTY is ok
				if ($validTo !== null) {
					if ($validTo !== "") {
						
						if (! $validator->isValid ( $validTo )) {
							$errors [] = 'End date is not correct or empty!';
						} else {
							$date_validated ++;
							$entity->setValidTo ( new \DateTime ( $validTo ) );
						}
					}
				}
				
				// all date corrected
				if ($date_validated == $date_to_validate) {
					
					if ($validFrom > $validTo) {
						$errors [] = 'End date must be in future!';
					}
				}
				
				$entity->setRemarks ( $remarks );
				
				$vendor = null;
				if ($vendor_id > 0) {
					$vendor = $this->doctrineEM->find ( 'Application\Entity\NmtBpVendor', $vendor_id );
					//$entity->setVendor ( $vendor );
				}
			
				$entity->setVendor ( $vendor );
				
				
				if (isset ( $_FILES ['attachments'] )) {
					$file_name = $_FILES ['attachments'] ['name'];
					$file_size = $_FILES ['attachments'] ['size'];
					$file_tmp = $_FILES ['attachments'] ['tmp_name'];
					$file_type = $_FILES ['attachments'] ['type'];
					$file_ext = strtolower ( end ( explode ( '.', $_FILES ['attachments'] ['name'] ) ) );
					
					// attachement required?
					if ($file_tmp == "" or $file_tmp === null) {
						
						//$errors [] = 'Attachment can\'t be empby!';
						
						if (count ( $errors ) > 0) {
							return new ViewModel ( array (
									'redirectUrl' => $redirectUrl,
									'errors' => $errors,
									'target' => $target,
									'entity' => $entity 
							) );
						}
						
						// update last change, without Attachment
						$this->doctrineEM->flush ();
						return $this->redirect ()->toUrl ( $redirectUrl );
					} else {
						
						$ext = '';
						$isPicture = 0;
						if (preg_match ( '/(jpg|jpeg)$/', $file_type )) {
							$ext = 'jpg';
							$isPicture = 1;
						} else if (preg_match ( '/(gif)$/', $file_type )) {
							$ext = 'gif';
							$isPicture = 1;
						} else if (preg_match ( '/(png)$/', $file_type )) {
							$ext = 'png';
							$isPicture = 1;
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
						
						// change target
						$criteria = array (
								"checksum" => $checksum,
								"project" => $target_id 
						);
						$ck = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationAttachment' )->findby ( $criteria );
						
						if (count ( $ck ) > 0) {
							$errors [] = 'Document: "' . $file_name . '"  exits already';
						}
						
						if (count ( $errors ) > 0) {
							
							return new ViewModel ( array (
									'redirectUrl' => $redirectUrl,
									'errors' => $errors,
									'target' => $target,
									'entity' => $entity 
							) );
						}
						;
						
						// deactive current on
						$entity->setIsactive(0);
						$entity->setMarkedForDeletion(1);
						//$entity->setRemarks();
						$entity->setLastChangeBy ( $u );
						$entity->setLastChangeOn ( new \DateTime () );
						
						
						$name = md5 ( $target_id . $checksum . uniqid ( microtime () ) ) . '_' . $this->generateRandomString () . '_' . $this->generateRandomString ( 10 ) . '.' . $ext;
						
						$folder_relative = $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
						$folder = ROOT . self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $folder_relative;
						
						if (! is_dir ( $folder )) {
							mkdir ( $folder, 0777, true ); // important
						}
						
						move_uploaded_file ( $file_tmp, "$folder/$name" );
						
						if ($ext == "pdf") {
							$pdf_box = ROOT . self::PDFBOX_FOLDER;
							
							// java -jar pdfbox-app-2.0.5.jar Encrypt [OPTIONS] <password> <inputfile>
							exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar Encrypt -O mla2017 -U ' . self::PDF_PASSWORD . ' ' . "$folder/$name" );
							
							// extract text:
							exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar ExtractText -password ' . self::PDF_PASSWORD . ' ' . "$folder/$name" . ' ' . "$folder/$name" . '.txt' );
						}
						
						// if new attachment upload, then clone new one
						$cloned_entity =  clone $entity;
						
						// copy new one
						$cloned_entity->setIsactive(1);
						$cloned_entity->setMarkedForDeletion(0);
						$cloned_entity->setChangeFor($entity->getId());
					
						$cloned_entity->setFilePassword ( $filePassword );
						$cloned_entity->setIsPicture ( $isPicture );
						$cloned_entity->setFilename ( $name );
						$cloned_entity->setFiletype ( $file_type );
						$cloned_entity->setFilenameOriginal ( $file_name );
						$cloned_entity->setSize ( $file_size );
						$cloned_entity->setFolder ( $folder );
						$cloned_entity->setFolderRelative ( $folder_relative . DIRECTORY_SEPARATOR );
						$cloned_entity->setChecksum ( $checksum );
						
						$cloned_entity->setCreatedBy ( $u );
						$cloned_entity->setCreatedOn ( new \DateTime () );
						$this->doctrineEM->persist ( $cloned_entity);
						$this->doctrineEM->flush ();
						
						return $this->redirect ()->toUrl ( $redirectUrl );
					}
				}
			}
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$entity_id = ( int ) $this->params ()->fromQuery ( 'entity_id' );
		
		// Target: Project
		$entity = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationAttachment', $entity_id );
		
		$target = null;
		if ($entity !== null) {
			$target = $entity->getProject ();
		}
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'target' => $target,
				'entity' => $entity 
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
		
		$target_id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		
		//Target: PROJECT
		$criteria = array (
				'project' => $target_id 
		);
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationAttachment' )->findBy ( $criteria );
		$total_records = count ( $list );
		$paginator = null;
		
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
		
		$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
				"email" => $this->identity () 
		) );
		
		if ($request->isPost ()) {
			
			$errors = array ();
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			
			$target_id = $request->getPost ( 'target_id' );
			// Target: Project
			$target = $this->doctrineEM->find ( 'Application\Entity\NmtPmProject', $target_id );
			
			if ($target == null) {
				
				$errors [] = 'Target object can\'t be empby!';
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'errors' => $errors,
						'target' => null,
						'entity' => null 
				) );
				
				// might need redirect
			} else {
				
				$vendor_id = $request->getPost ( 'vendor_id' );
				$documentSubject = $request->getPost ( 'documentSubject' );
				$validFrom = $request->getPost ( 'validFrom' );
				$validTo = $request->getPost ( 'validTo' );
				$validFrom = $request->getPost ( 'validFrom' );
				
				$isActive = $request->getPost ( 'isActive' );
				$markedForDeletion = $request->getPost ( 'markedForDeletion' );
				$filePassword = $request->getPost ( 'filePassword' );
				$visibility = $request->getPost ( 'visibility' );
				
				$entity = new NmtApplicationAttachment ();
				
				// Target: PROJECT
				$entity->setProject ( $target );
				
				$remarks = $request->getPost ( 'remarks' );
				
				if ($documentSubject == null) {
					$errors [] = 'Please give document subject!';
				} else {
					$entity->setDocumentSubject ( $documentSubject );
				}
				
				if ($isActive != 1) {
					$isActive = 0;
				}
				
				if ($markedForDeletion != 1) {
					$markedForDeletion = 0;
				}
				
				if ($visibility != 1) {
					$visibility = 0;
				}
				
				if ($filePassword === null or $filePassword == "") {
					$filePassword = self::PDF_PASSWORD;
				}
				
				$entity->setIsActive ( $isActive );
				$entity->setMarkedForDeletion ( $markedForDeletion );
				$entity->setVisibility ( $visibility );
				// validator.
				$validator = new Date ();
				$date_to_validate = 2;
				$date_validated = 0;
				
				// Empty is OK
				if ($validFrom !== null) {
					if ($validFrom !== "") {
						if (! $validator->isValid ( $validFrom )) {
							$errors [] = 'Start date is not correct or empty!';
						} else {
							$date_validated ++;
							$entity->setValidFrom ( new \DateTime ( $validFrom ) );
						}
					}
				}
				
				// Empty is OK
				if ($validTo !== null) {
					if ($validTo !== "") {
						
						if (! $validator->isValid ( $validTo )) {
							$errors [] = 'End date is not correct or empty!';
						} else {
							$date_validated ++;
							$entity->setValidTo ( new \DateTime ( $validTo ) );
						}
					}
				}
				
				// all date corrected
				if ($date_validated == $date_to_validate) {
					
					if ($validFrom > $validTo) {
						$errors [] = 'End date must be in future!';
					}
				}
				
				$entity->setRemarks ( $remarks );
				
				// need to set context id
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
					
					// attachement required?
					if ($file_tmp == "" or $file_tmp === null) {
						
						$errors [] = 'Attachment can\'t be empty!';
						return new ViewModel ( array (
								'redirectUrl' => $redirectUrl,
								'errors' => $errors,
								'target' => $target,
								'entity' => $entity 
						) );
					} else {
						
						$ext = '';
						$isPicture = 0;
						if (preg_match ( '/(jpg|jpeg)$/', $file_type )) {
							$ext = 'jpg';
							$isPicture = 1;
						} else if (preg_match ( '/(gif)$/', $file_type )) {
							$ext = 'gif';
							$isPicture = 1;
						} else if (preg_match ( '/(png)$/', $file_type )) {
							$ext = 'png';
							$isPicture = 1;
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
							$errors [] = 'File size must be  2 MB';
						}
						
						$checksum = md5_file ( $file_tmp );
						
						// change target
						$criteria = array (
								"checksum" => $checksum,
								"project" => $target_id 
						);
						$ck = $this->doctrineEM->getRepository ( 'Application\Entity\NmtApplicationAttachment' )->findby ( $criteria );
						
						if (count ( $ck ) > 0) {
							$errors [] = 'Document: "' . $file_name . '"  exits already';
						}
						
						if (count ( $errors ) > 0) {
							
							return new ViewModel ( array (
									'redirectUrl' => $redirectUrl,
									'errors' => $errors,
									'target' => $target,
									'entity' => $entity 
							) );
						}
						;
						
						$name = md5 ( $target_id . $checksum . uniqid ( microtime () ) ) . '_' . $this->generateRandomString () . '_' . $this->generateRandomString ( 10 ) . '.' . $ext;
						
						$folder_relative = $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
						$folder = ROOT . self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $folder_relative;
						
						if (! is_dir ( $folder )) {
							mkdir ( $folder, 0777, true ); // important
						}
						
						// echo ("$folder/$name");
						move_uploaded_file ( $file_tmp, "$folder/$name" );
						
						if ($ext == "pdf") {
							$pdf_box = ROOT . self::PDFBOX_FOLDER;
							
							// java -jar pdfbox-app-2.0.5.jar Encrypt [OPTIONS] <password> <inputfile>
							exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar Encrypt -O mla2017 -U ' . $filePassword . ' ' . "$folder/$name" );
							
							// extract text:
							exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar ExtractText -password ' . $filePassword . ' ' . "$folder/$name" . ' ' . "$folder/$name" . '.txt' );
						}
						// update database
						$entity->setFilePassword ( $filePassword );
						$entity->setIsPicture ( $isPicture );
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
			}
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$target_id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		
		// Target: Project
		$target = $this->doctrineEM->find ( 'Application\Entity\NmtPmProject', $target_id );
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'target' => $target,
				'entity' => null 
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
		$target_id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		
		$attachment = new NmtApplicationAttachment ();
		$tmp_attachment = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationAttachment', $target_id );
		$attachment = $tmp_attachment;
		
		$f = ROOT . self::ATTACHMENT_FOLDER . DIRECTORY_SEPARATOR . $attachment->getFolderRelative () . $attachment->getFilename ();
		$output = file_get_contents ( $f );
		
		$response = $this->getResponse ();
		$headers = new Headers ();
		
		$headers->addHeaderLine ( 'Content-Type: ' . $attachment->getFiletype () );
		$headers->addHeaderLine ( 'Content-Disposition: attachment; filename="' . $attachment->getFilenameOriginal () . '"' );
		$headers->addHeaderLine ( 'Content-Description: File Transfer' );
		$headers->addHeaderLine ( 'Content-Transfer-Encoding: binary' );
		$headers->addHeaderLine ( 'Content-Encoding: UTF-8' );
		
		$response->setHeaders ( $headers );
		
		$response->setContent ( $output );
		return $response;
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
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
