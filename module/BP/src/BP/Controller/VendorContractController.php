<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace BP\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use Application\Entity\NmtBpVendorContract;
use Zend\Validator\Date;
use MLA\Paginator;
use Zend\Http\Headers;

/*
 * Control Panel Controller
 */
class VendorContractController extends AbstractActionController {
	protected $doctrineEM;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		$em = $this->doctrineEM;
		$data = $em->getRepository ( 'Application\Entity\NmtWfWorkflow' )->findAll ();
		foreach ( $data as $row ) {
			echo $row->getWorkflowName ();
			echo '<br />';
		}
	}
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
	 */
	public function addAction() {
		$request = $this->getRequest ();
		
		$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
				'email' => $this->identity () 
		) );
		
		if ($request->isPost ()) {
			
			$vendor_id = $request->getPost ( 'vendor_id' );
			
			$vendor = null;
			if ($vendor_id > 0) {
				$vendor = $this->doctrineEM->find ( 'Application\Entity\NmtBpVendor', $vendor_id );
			}
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			
			if (isset ( $_FILES ['attachments'] )) {
				$file_name = $_FILES ['attachments'] ['name'];
				$file_size = $_FILES ['attachments'] ['size'];
				$file_tmp = $_FILES ['attachments'] ['tmp_name'];
				$file_type = $_FILES ['attachments'] ['type'];
				$file_ext = strtolower ( end ( explode ( '.', $_FILES ['attachments'] ['name'] ) ) );
				
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
				
				// acept only PDF
				$expensions = array (
						"pdf" 
				);
				
				$isActive = $request->getPost ( 'isActive' );
				$contractSubject = $request->getPost ( 'contractSubject' );
				$keywords = $request->getPost ( 'keywords' );
				$signingDate = $request->getPost ( 'signingDate' );
				$validFrom = $request->getPost ( 'validFrom' );
				$validTo = $request->getPost ( 'validTo' );
				$remarks = $request->getPost ( 'remarks' );
				
				$entity = new NmtBpVendorContract ();
				
				$entity->setContractSubject ( $contractSubject );
				$entity->setKeywords ( $keywords );
					$entity->setIsActive ( $isActive );
				$entity->setVendor ( $vendor );
				$entity->setRemarks ( $remarks );
				
				//var_dump(new  \DateTime($signingDate));
				
				$errors = array ();
				
				if ($contractSubject == null) {
					$errors [] = 'Please give short subject for the contract';
				}
				
				// validator.
				$date_validated = 0;
				$validator = new Date ();
				
				$errors = array ();
				if (! $validator->isValid ( $signingDate )) {
					$errors [] = 'Signing date is not correct or empty!';
				} else {
					$entity->setSigningDate ( new \DateTime($signingDate) );
					$date_validated ++;
				}
				
				if (! $validator->isValid ($validFrom )) {
					$errors [] = 'From date is not correct or empty!';
				} else {
					$date_validated ++;
					$entity->setValidFrom ( new \DateTime($validFrom ));
					
				}
				
				if (! $validator->isValid ( $validTo )) {
					$errors [] = 'To date is not correct or empty!';
				} else {
					$date_validated ++;
					$entity->setValidTo (new \DateTime($validTo) );
					
				}
				
				// all date corrected
				if ($date_validated == 3) {
					
					if ($signingDate > $validFrom) {
						$errors [] = 'Contract is effective backwards!';
					} else {
						if ($validFrom > $validTo) {
							$errors [] = 'To date must > from date!';
						}
					}
				}
				
				
				if ($file_size > 2097152) {
					$errors [] = 'File size must be excately 2 MB';
				}
				
				$checksum = null;
				if ($file_tmp == null) {
					$errors [] = 'Attachment file cannot be empty';
				} else {
					
					if (in_array ( $ext, $expensions ) === false) {
						$errors [] = 'Extension file "' . $ext . '" not supported, please choose a "pdf"';
					}
					
					$checksum = md5_file ( $file_tmp );
					$criteria = array (
							"checksum" => $checksum,
							"vendor" => $vendor_id 
					);
					$ck = $this->doctrineEM->getRepository ( 'Application\Entity\NmtBpVendorContract' )->findby ( $criteria );
					
					if (count ( $ck ) > 0) {
						$errors [] = 'Attachment file: "' . $file_name . '"  exits already';
					}
				}
				
				if ($vendor == null) {
					$errors [] = 'Vendor can\'t be empty';
				}
				
				if (count ( $errors ) > 0) {
					
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'errors' => $errors,
							'entity' => $entity,
							'vendor' => $vendor 
					
					) );
				}
				;
				
				// OK NOW
				
				$name = md5 ( $vendor_id . $checksum . uniqid ( microtime () ) ) . '_' . $this->generateRandomString () . '_' . $this->generateRandomString ( 10 ) . '.' . $ext;
				
				$root_dir = ROOT . "/data/bp/vendor/contract";
				$folder_relative = $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
				$folder = $root_dir . DIRECTORY_SEPARATOR . $folder_relative;
				
				if (! is_dir ( $folder )) {
					mkdir ( $folder, 0777, true ); // important
				}
				
				move_uploaded_file ( $file_tmp, "$folder/$name" );
				
				$pdf_box = ROOT . "/vendor/pdfbox/";
				
				// java -jar pdfbox-app-2.0.5.jar Encrypt [OPTIONS] <password> <inputfile>
				exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar Encrypt -O mla2017 -U mla2017 ' . "$folder/$name" );
				
				// extract text:
				exec ( 'java -jar ' . $pdf_box . '/pdfbox-app-2.0.5.jar ExtractText -password mla2017 ' . "$folder/$name" . ' ' . "$folder/$name" . '.txt' );
				
				// echo('java -jar ' . $pdf_box.'/pdfbox-app-2.0.5.jar Encrypt -O nmt -U nmt '."$folder/$name");
				
				$entity->setFiletype( $file_type);
				
				$entity->setFilename ( $name );
				$entity->setFilenameOriginal ( $file_name );
				$entity->setSize ( $file_size );
				
				$entity->setFolder ( $folder );
				$entity->setFolderRelative ( $folder_relative.DIRECTORY_SEPARATOR);
				$entity->setChecksum ( $checksum );
				
				$entity->setCreatedBy ( $u );
				$entity->setCreatedOn ( new \DateTime () );
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				
				return $this->redirect ()->toUrl ( $redirectUrl );
			}
			
			return new ViewModel ( array (
					'redirectUrl' => $redirectUrl,
					'errors' => null,
					'entity' => null,
					'vendor' => $vendor 
			) );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$vendor_id = ( int ) $this->params ()->fromQuery ( 'vendor_id' );
		$vendor = null;
		if ($vendor_id > 0) {
			$vendor = $this->doctrineEM->find ( 'Application\Entity\NmtBpVendor', $vendor_id );
		}
		
		return new ViewModel ( array (
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'entity' => null,
				'vendor' => $vendor 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
		$sort_criteria = array ();
		$criteria = array ();
		
		$is_active = $this->params ()->fromQuery ( 'is_active' );
		$sort_by = $this->params ()->fromQuery ( 'sort_by' );
		$sort = $this->params ()->fromQuery ( 'sort' );
		
		$criteria1 = array ();
		if (! $is_active == null) {
			$criteria1 = array (
					"isActive" => $is_active
			);
			
			if ($is_active == - 1) {
				$criteria1 = array (
						"isActive" => '0'
				);
			}
		}
		
		if ($sort_by == null) :
		$sort_by = "contractSubject";
		endif;
		
		if ($sort == null) :
		$sort = "ASC";
		endif;
		
		$sort_criteria = array (
				$sort_by => $sort
		);
		
		// $criteria = array_merge ( $criteria1, $criteria2, $criteria3);
		// var_dump($criteria);
		$criteria = $criteria1;
		
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
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtBpVendorContract' )->findBy ( $criteria, $sort_criteria );
		$total_records = count ( $list );
		$paginator = null;
		
		if ($total_records > $resultsPerPage) {
			$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtBpVendorContract' )->findBy ( $criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => $paginator,
				'sort_by' => $sort_by,
				'sort' => $sort,
				'is_active' => $is_active,
				'per_pape' => $resultsPerPage
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function downloadAction() {
		
		// $request = $this->getRequest ();
		$root_dir = ROOT . "/data/bp/vendor/contract/";
		$target_id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		
		$item_attachment = new NmtBpVendorContract();
		$tmp_attachment = $this->doctrineEM->find ( 'Application\Entity\NmtBpVendorContract', $target_id );
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
	 * @return \Doctrine\ORM\EntityManager
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
