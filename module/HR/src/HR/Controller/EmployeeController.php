<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace HR\Controller;

use Zend\I18n\Validator\Int;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;
use Zend\Http\Headers;

use MLA\Paginator;
use MLA\Files;
use Inventory\Model\SparepartPicture;
use Inventory\Model\SparepartPictureTable;
use Inventory\Model\MLASparepart;
use Inventory\Model\MLASparepartTable;
use Inventory\Model\SparepartCategoryMember;
use Inventory\Model\SparepartCategoryMemberTable;
use Inventory\Model\SparepartCategoryTable;
use Inventory\Model\SparepartMovement;
use Inventory\Model\SparepartMovementsTable;
use Inventory\Services\SparepartService;
use Inventory\Model\SparepartMinimumBalance;
use Inventory\Model\SparepartMinimumBalanceTable;
use Procurement\Model\PurchaseRequestCartItem;
use Procurement\Model\PurchaseRequestCartItemTable;
use Procurement\Model\PurchaseRequestItemTable;

use HR\Model\Employee;
use HR\Model\EmployeeTable;
use HR\Model\EmployeePicture;
use HR\Model\EmployeePictureTable;
use HR\Services\EmployeeService;
use HR\Services\EmployeeSearchService;

use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\MultiTerm;
use ZendSearch\Lucene\Search\Query\Wildcard;
use Procurement\Services\VendorSearchService;
use \Exception;


class EmployeeController extends AbstractActionController {
	protected $authService;
	protected $SmtpTransportService;
	protected $userTable;
	
	protected $employeeTable;
	protected $employeePictureTable;
	protected $employeeService;
	protected $employeeSearchService;
	
	private $tmp_path = "module/HR/data/tmp";
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel|\Zend\Stdlib\ResponseInterface
	 */
	public function searchAction() {
		$q = $this->params ()->fromQuery ( 'query' );
		$json = ( int ) $this->params ()->fromQuery ( 'json' );
	
		if ($q == '') {
			return new ViewModel ( array (
					'hits' => null
			) );
		}
	
		$error = null;
		try {
				
			if (strpos ( $q, '*' ) != false) {
				$pattern = new Term ( $q );
				$query = new Wildcard ( $pattern );
				$hits = $this->employeeSearchService->search ( $query );
			} else {
				$hits = $this->employeeSearchService->search ( $q );
			}
		} catch ( Exception $e ) {
			$error = $e->getMessage ();
			$hits=null;
		}
	
		if ($json === 1) {
			$data = array ();
			if ($hits != null) {
				foreach ( $hits as $key => $value ) {
					$n = ( int ) $key;
					$data [$n] ['id'] = $value->employee_id;
					$data [$n] ['employee_name'] = $value->employee_name;
					$data [$n] ['employee_name_local'] = $value->employee_name_local;
				}
			}
			$response = $this->getResponse ();
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
			$response->setContent ( json_encode ( $data ) );
			return $response;
		}
	
		return new ViewModel ( array (
				'query' => $q,
				'hits' => $hits,
				'error' => $error,
		) );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createIndexAction() {
		$searcher = $this->employeeSearchService;
		$message = $searcher->createIndex();
	
		return new ViewModel ( array (
				'message' => $message,
		) );
	
	}
	
	/**
	 * create new spare part
	 */
	public function addAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			if ($request->isPost ()) {
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$input = new MLASparepart ();
				$input->name = $request->getPost ( 'name' );
				$input->name_local = $request->getPost ( 'name_local' );
				
				$input->description = $request->getPost ( 'description' );
				$input->code = $request->getPost ( 'code' );
				$input->tag = $request->getPost ( 'tag' );
				
				$input->location = $request->getPost ( 'location' );
				$input->comment = $request->getPost ( 'comment' );
				
				$category_id = ( int ) $request->getPost ( 'category_id' );
				
				$errors = array ();
				
				// tag must be unique
				
				if ($this->sparePartTable->isTagExits ( $input->tag ) === true) {
					$errors [] = 'Sparepart with tag number "' . $input->tag . '" exits already in database';
				}
				
				if ($input->name == '') {
					$errors [] = 'Please give spare-part name';
				}
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							'errors' => $errors,
							'redirectUrl' => $redirectUrl,
							'category_id' => $category_id,
							'sparepart' => $input 
					) );
				}
				
				$newId = $this->sparePartTable->add ( $input );
				$root_dir = $this->sparePartService->getPicturesPath ();
				
				// $files = $request->getFiles ()->toArray ();
				
				$pictureUploadListener = $this->getServiceLocator ()->get ( 'Inventory\Listener\PictureUploadListener' );
				$this->getEventManager ()->attachAggregate ( $pictureUploadListener );
				
				$id = $newId;
				
				foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
					if ($error == UPLOAD_ERR_OK) {
						$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
						
						$ext = strtolower ( pathinfo ( $_FILES ["pictures"] ["name"] [$key], PATHINFO_EXTENSION ) );
						
						if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png') {
							
							$checksum = md5_file ( $tmp_name );
							
							if (! $this->sparePartPictureTable->isChecksumExits ( $id, $checksum )) {
								
								$name = md5 ( $id . $checksum . uniqid ( microtime () ) ) . '.' . $ext;
								$folder = $root_dir . DIRECTORY_SEPARATOR . $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
								
								if (! is_dir ( $folder )) {
									mkdir ( $folder, 0777, true ); // important
								}
								
								$ftype = $_FILES ["pictures"] ["type"] [$key];
								move_uploaded_file ( $tmp_name, "$folder/$name" );
								
								// add pictures
								$pic = new SparepartPicture ();
								$pic->url = "$folder/$name";
								$pic->filetype = $ftype;
								$pic->sparepart_id = $id;
								$pic->filename = "$name";
								$pic->folder = "$folder";
								$pic->checksum = $checksum;
								
								$this->sparePartPictureTable->add ( $pic );
								
								// trigger uploadPicture
								$this->getEventManager ()->trigger ( 'uploadPicture', __CLASS__, array (
										'picture_name' => $name,
										'pictures_dir' => $folder 
								) );
							}
						}
					}
				}
				
				// add category
				if ($category_id > 1) {
					$m = new SparepartCategoryMember ();
					$m->sparepart_id = $newId;
					$m->sparepart_cat_id = $category_id;
					$this->sparePartCategoryMemberTable->add ( $m );
				}
				
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$category_id = ( int ) $this->params ()->fromQuery ( 'category_id' );
		
		// add category
		if ($category_id > 1) {
			$category = $this->sparePartCategoryTable->get ( $category_id );
		} else {
			$category = null;
		}
		
		return new ViewModel ( array (
				'message' => 'Add new Sparepart',
				'category' => $category,
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'sparepart' => null 
		) );
	}
	
	/**
	 * Edit Spare part
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$id = $request->getPost ( 'id' );
			
			$input = new MLASparepart ();
			$input->id = $id;
			$input->name = $request->getPost ( 'name' );
			$input->name_local = $request->getPost ( 'name_local' );
			
			$input->description = $request->getPost ( 'description' );
			$input->code = $request->getPost ( 'code' );
			$input->tag = $request->getPost ( 'tag' );
			
			$input->location = $request->getPost ( 'location' );
			$input->comment = $request->getPost ( 'comment' );
			
			$errors = array ();
			
			if ($input->name == '') {
				$errors [] = 'Please give spare-part name';
			}
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			
			if (count ( $errors ) > 0) {
				// return current sp
				$sparepart = $this->sparePartTable->get ( $input->id );
				
				return new ViewModel ( array (
						'errors' => $errors,
						'redirectUrl' => $redirectUrl,
						'sparepart' => $sparepart 
				) );
			}
			
			$this->sparePartTable->update ( $input, $input->id );
			$root_dir = $this->sparePartService->getPicturesPath ();
			
			$pictureUploadListener = $this->getServiceLocator ()->get ( 'Inventory\Listener\PictureUploadListener' );
			$this->getEventManager ()->attachAggregate ( $pictureUploadListener );
			
			foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
				if ($error == UPLOAD_ERR_OK) {
					$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
					
					$ext = strtolower ( pathinfo ( $_FILES ["pictures"] ["name"] [$key], PATHINFO_EXTENSION ) );
					
					if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png') {
						
						$checksum = md5_file ( $tmp_name );
						
						if (! $this->sparePartPictureTable->isChecksumExits ( $id, $checksum )) {
							
							$name = md5 ( $id . $checksum . uniqid ( microtime () ) ) . '.' . $ext;
							$folder = $root_dir . DIRECTORY_SEPARATOR . $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
							
							if (! is_dir ( $folder )) {
								mkdir ( $folder, 0777, true ); // important
							}
							
							$ftype = $_FILES ["pictures"] ["type"] [$key];
							move_uploaded_file ( $tmp_name, "$folder/$name" );
							
							// add pictures
							$pic = new SparepartPicture ();
							$pic->url = "$folder/$name";
							$pic->filetype = $ftype;
							$pic->sparepart_id = $id;
							$pic->filename = "$name";
							$pic->folder = "$folder";
							$pic->checksum = $checksum;
							
							$this->sparePartPictureTable->add ( $pic );
							
							// trigger uploadPicture
							$this->getEventManager ()->trigger ( 'uploadPicture', __CLASS__, array (
									'picture_name' => $name,
									'pictures_dir' => $folder 
							) );
						}
					}
				}
			}
			
			$this->redirect ()->toUrl ( $redirectUrl );
			
			// return $this->redirect ()->toRoute ( 'Spareparts_Category');
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$sparepart = $this->sparePartTable->get ( $id );
		
		return new ViewModel ( array (
				'sparepart' => $sparepart,
				'redirectUrl' => $redirectUrl,
				'errors' => null 
		) );
	}
	
	/**
	 * Edit Spare part
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function uploadPictureAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$id = $request->getPost ( 'id' );
			$root_dir = $this->sparePartService->getPicturesPath ();
			
			// $files = $request->getFiles ()->toArray ();
			
			$pictureUploadListener = $this->getServiceLocator ()->get ( 'Inventory\Listener\PictureUploadListener' );
			$this->getEventManager ()->attachAggregate ( $pictureUploadListener );
			
			foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
				if ($error == UPLOAD_ERR_OK) {
					$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
					
					$ext = strtolower ( pathinfo ( $_FILES ["pictures"] ["name"] [$key], PATHINFO_EXTENSION ) );
					
					if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'gif' || $ext == 'png') {
						
						$checksum = md5_file ( $tmp_name );
						
						if (! $this->sparePartPictureTable->isChecksumExits ( $id, $checksum )) {
							
							$name = md5 ( $id . $checksum . uniqid ( microtime () ) ) . '.' . $ext;
							$folder = $root_dir . DIRECTORY_SEPARATOR . $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
							
							if (! is_dir ( $folder )) {
								mkdir ( $folder, 0777, true ); // important
							}
							
							$ftype = $_FILES ["pictures"] ["type"] [$key];
							move_uploaded_file ( $tmp_name, "$folder/$name" );
							
							// add pictures
							$pic = new SparepartPicture ();
							$pic->url = "$folder/$name";
							$pic->filetype = $ftype;
							$pic->sparepart_id = $id;
							$pic->filename = "$name";
							$pic->folder = "$folder";
							$pic->checksum = $checksum;
							
							$this->sparePartPictureTable->add ( $pic );
							
							// trigger uploadPicture
							$this->getEventManager ()->trigger ( 'uploadPicture', __CLASS__, array (
									'picture_name' => $name,
									'pictures_dir' => $folder 
							) );
						}
					}
				}
			}
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$this->redirect ()->toUrl ( $redirectUrl );
			
			// return $this->redirect ()->toRoute ( 'Spareparts_Category');
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$sparepart = $this->sparePartTable->get ( $id );
		
		return new ViewModel ( array (
				'sparepart' => $sparepart,
				'redirectUrl' => $redirectUrl,
				'errors' => null 
		) );
	}
	
	/**
	 * Upload resized pictures
	 *
	 * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
	 */
	public function uploadPicture1Action() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		if ($request->isPost ()) {
			
			$pictures = $_POST ['pictures'];
			$id = $_POST ['employee_id'];
			
			foreach ( $pictures as $p ) {
				
				$filetype = $p [0];
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
				
				$root_dir = $this->employeeService->getPicturesPath ();
				
				$pictureUploadListener = $this->getServiceLocator ()->get ( 'Inventory\Listener\PictureUploadListener' );
				$this->getEventManager ()->attachAggregate ( $pictureUploadListener );
				
				if (! $this->employeePictureTable->isChecksumExits ( $id, $checksum )) {
					$name = md5 ( $id . $checksum . uniqid ( microtime () ) ) . '.' . $ext;
					
					$folder = $root_dir . DIRECTORY_SEPARATOR . $name [0] . $name [1] . DIRECTORY_SEPARATOR . $name [2] . $name [3] . DIRECTORY_SEPARATOR . $name [4] . $name [5];
					
					if (! is_dir ( $folder )) {
						mkdir ( $folder, 0777, true ); // important
					}
					
					rename ( $tmp_name, "$folder/$name" );
					
					// add pictures
					$pic = new EmployeePicture ();
					$pic->url = "$folder/$name";
					$pic->filetype = $filetype;
					$pic->employee_id = $id;
					$pic->filename = "$name";
					$pic->folder = "$folder";
					$pic->checksum = $checksum;
					$this->employeePictureTable->add ( $pic );
					
					// trigger uploadPicture
					$this->getEventManager ()->trigger ( 'uploadPicture', __CLASS__, array (
							'picture_name' => $name,
							'pictures_dir' => $folder 
					) );
				}
			}
			
			$data = array ();
			$data ['id'] = $id;
			// $data['filetype'] = $filetype;
			
			$response = $this->getResponse ();
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
			$response->setContent ( json_encode ( $data ) );
			return $response;
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$employee = $this->employeeTable->get ( $id );
		
		return new ViewModel ( array (
				'employee' => $employee,
				'redirectUrl' => $redirectUrl,
				'errors' => null 
		) );
	}
	
	/**
	 * List all employee
	 */
	public function listAction() {
		$output = $this->params ()->fromQuery ( 'output' );
		
		if ($output === 'csv') {
				
			$fh = fopen ( 'php://memory', 'w' );
			// $myfile = fopen('ouptut.csv', 'a+');
				
			$h = array ();
			$h [] = "No.";
			$h [] = "Code";
			$h [] = "Name";
			$h [] = "Name Local";
			$h [] = "Gender";
			$h [] = "Birthday";
			$h [] = "Remarks";
				
			$delimiter = ";";
				
			fputcsv ( $fh, $h, $delimiter, '"' );
			// fputs($fh, implode($h, ',')."\n");
				
			$employees = $this->employeeTable->getEmployees( 0, 0 );
				
			foreach ( $employees as $m ) {
				$l = array ();
				$l [] = ( string ) $m->id;
				
				$str = $m->employee_code;
				// force certain number/date formats to be imported as strings
				if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
					$str = "'$str";
				}
				
				$l [] = ( string ) $str;
				$l [] = ( string ) $m->employee_name;
				$l [] = ( string ) utf8_encode($m->employee_name_local);
				$l [] = ( string ) $m->employee_gender;
				$l [] = ( string ) date_format(date_create($m->employee_dob),"d-m-Y") ;
				$l [] = ( string ) $m->remarks;
				
				fputcsv ( $fh, $l, $delimiter, '"' );
				// fputs($fh, implode($l, ',')."\n");
			}
				
			$fileName = 'spareparts-'.date( "m-d-Y" ) .'-' . date("h:i:s").'.csv';
			fseek ( $fh, 0 );
			$output = stream_get_contents ( $fh );
			// file_put_contents($fileName, $output);
				
			$response = $this->getResponse ();
			$headers = new Headers();
				
			$headers->addHeaderLine ( 'Content-Type: text/csv' );
			//$headers->addHeaderLine ( 'Content-Type: application/vnd.ms-excel; charset=UTF-8' );
				
			$headers->addHeaderLine ( 'Content-Disposition: attachment; filename="' . $fileName . '"' );
			$headers->addHeaderLine ( 'Content-Description: File Transfer' );
			$headers->addHeaderLine ( 'Content-Transfer-Encoding: binary' );
			$headers->addHeaderLine ( 'Content-Encoding: UTF-8' );
				
			//$response->setHeaders(Headers::fromString("Content-Type: application/octet-stream\r\nContent-Length: 9\r\nContent-Disposition: attachment; filename=\"blamoo.txt\""));
			$response->setHeaders($headers);
			// $output = fread($fh, 8192);
				
			$response->setContent ( $output );
				
			fclose ( $fh );
			// unlink($fileName);
			return $response;
		}
		
		if ($output === 'xls') {
				
			$filename = $this->tmp_path . "/employees-".date( "m-d-Y" ) . ".xls";

			$realPath = realpath( $filename );
			if ( false === $realPath )
			{
				touch( $filename );
				chmod( $filename, 0777 );
			}
			$filename = realpath( $filename );
			
			
			$handle = fopen( $filename, "w" );
			
			$finalData = array();
			
			$employees = $this->employeeTable->getEmployees(0, 0);
			
			$delimiter = ";";
			
			foreach ( $employees as $m ) {
				$finalData = array ();
				$finalData [] = ( string ) $m->id;
				$finalData [] = ( string ) "'" . $m->employee_code;
				$finalData [] = ( string ) $m->employee_name;
				$finalData [] = ( string ) $m->employee_name_local;
				$finalData [] = ( string ) $m->employee_gender;
				$finalData [] = ( string ) date_format(date_create($m->employee_dob),"d-m-Y");
				$finalData [] = ( string ) $m->remarks;
					
				fputcsv( $handle, $finalData,$delimiter );
			}
			
			
			
			fclose( $handle );
			
			$output = readfile( $filename );
			
			$response = $this->getResponse ();
			$headers = new Headers ();
				
			//$headers->addHeaderLine ( 'Content-Type: text/csv' );
			$headers->addHeaderLine ( 'Content-Type: application/vnd.ms-excel; charset=UTF-8' );
				
			$headers->addHeaderLine ( 'Content-Disposition: attachment; filename=employee.xls' );
			$headers->addHeaderLine ( 'Content-Description: File Transfer' );
			$headers->addHeaderLine ( 'Content-Transfer-Encoding: binary' );
			$headers->addHeaderLine ( 'Content-Encoding: UTF-8' );
				
			// $response->setHeaders(Headers::fromString("Content-Type: application/octet-stream\r\nContent-Length: 9\r\nContent-Disposition: attachment; filename=\"blamoo.txt\""));
			$response->setHeaders ( $headers );
			// $output = fread($fh, 8192);
					
			$response->setContent ( $output );
							
			return $response;
		}
		
		// normal output
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 15;
		} else {
			$resultsPerPage = $this->params ()->fromQuery ( 'perPage' );
		}
		
		if (is_null ( $this->params ()->fromQuery ( 'page' ) )) {
			$page = 1;
		} else {
			$page = $this->params ()->fromQuery ( 'page' );
		}
		$totalResults = $this->employeeTable->getTotalEmployee ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$employeess = $this->employeeTable->getEmployees ( ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		} else {
			$employeess = $this->employeeTable->getEmployees ( 0, 0 );
		}
		
		return new ViewModel ( array (
				'total_employees' => $totalResults,
				'employees' => $employeess,
				'paginator' => $paginator 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function list1Action() {
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
		
		$spareparts = $this->sparePartTable->fetchAll ();
		$totalResults = $spareparts->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$spareparts = $this->sparePartTable->getLimitSpareParts ( ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		$this->layout ( 'layout/no-layout' );
		
		return new ViewModel ( array (
				'total_spareparts' => $totalResults,
				'spareparts' => $spareparts,
				'paginator' => $paginator 
		) );
	}
	
	/**
	 * Show detail of a spare parts
	 */
	public function showAction() {
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$employee = $this->employeeTable->get ( $id );
		$pictures = $this->employeePictureTable->getPicturesById($id);
		
		return new ViewModel ( array (
				'employee' => $employee,
				'pictures' => $pictures,
				'redirectUrl' => $redirectUrl 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function picturesAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'sparepart_id' );
		$sp = $this->sparePartTable->get ( $id );
		$pictures = $this->sparePartPictureTable->getSparepartPicturesById ( $id );
		
		return new ViewModel ( array (
				'sparepart' => $sp,
				'pictures' => $pictures 
		) );
	}
	
	// SETTER AND GETTER
	public function getEmployeeTable() {
		return $this->employeeTable;
	}
	public function setEmployeeTable(EmployeeTable $employeeTable) {
		$this->employeeTable = $employeeTable;
		return $this;
	}
	public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable($userTable) {
		$this->userTable = $userTable;
		return $this;
	}
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
	public function getEmployeeService() {
		return $this->employeeService;
	}
	public function setEmployeeService(EmployeeService $employeeService) {
		$this->employeeService = $employeeService;
		return $this;
	}
	public function getEmployeePictureTable() {
		return $this->employeePictureTable;
	}
	public function setEmployeePictureTable(EmployeePictureTable $employeePictureTable) {
		$this->employeePictureTable = $employeePictureTable;
		return $this;
	}
	public function getEmployeeSearchService() {
		return $this->employeeSearchService;
	}
	public function setEmployeeSearchService(EmployeeSearchService $employeeSearchService) {
		$this->employeeSearchService = $employeeSearchService;
		return $this;
	}
	
	
	
	
	
	
	
	
	
}
