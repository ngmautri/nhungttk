<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\I18n\Validator\Int;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use MLA\Files;
use Application\Model\Department;
use Application\Model\DepartmentTable;
use Application\Model\DepartmentMember;
use Application\Model\DepartmentMemberTable;
use Application\Model\CurrencyTable;

/*
 * Control Panel Controller
 */
class CurrencyController extends AbstractActionController {
	protected $SmtpTransportService;
	protected $authService;
	protected $userTable;
	
	protected $currencyTable;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 * create New Article
	 */
	public function addAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		if ($request->isPost ()) {
			
			if ($request->isPost ()) {
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$input = new Department ();
				$input->name = $request->getPost ( 'name' );
				$input->short_name = $request->getPost ( 'short_name' );
				
				$input->description = $request->getPost ( 'description' );
				$input->status = $request->getPost ( 'status' );
				$input->created_by = $user ["id"];
				
				$errors = array ();
				
				if ($input->name == '') {
					$errors [] = 'Please give department name';
				}
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							'errors' => $errors,
							'redirectUrl' => $redirectUrl,
							'department' => $input 
					) );
				}
				
				$this->departmentTable->add ( $input );
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		return new ViewModel ( array (
				'message' => 'Add new department',
				'redirectUrl' => $redirectUrl,
				'errors' => null,
				'department' => null 
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
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
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
		
		$departments = $this->departmentTable->getDepartments();
		$totalResults = $departments->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$departments = $this->departmentTable->getLimitDepartments ( ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'total_departments' => $totalResults,
				'departments' => $departments,
				'paginator' => $paginator 
		) );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function showAction() {
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) )) {
			$resultsPerPage = 20;
		} else {
			$resultsPerPage = $this->params ()->fromQuery ( 'perPage' );
		}
	
		if (is_null ( $this->params ()->fromQuery ( 'page' ) )) {
			$page = 1;
		} else {
			$page = $this->params ()->fromQuery ( 'page' );
		}
	
		$id = $this->params ()->fromQuery ( 'id' );
	
		$department= $this->departmentTable->get ( $id );
	
		$users = $this->departmentMemberTable->getMembersOf($id );
		$totalResults = $users->count ();
	
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$users = $this->departmentMemberTable->getLimitMembersOf($id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
	
		return new ViewModel ( array (
				'department' => $department,
				'total_users' => $totalResults,
				'users' => $users,
				'paginator' => $paginator
		) );
	}
	
	// SETTER AND GETTER
	public function getSmtpTransportService() {
		return $this->SmtpTransportService;
	}
	public function setSmtpTransportService($SmtpTransportService) {
		$this->SmtpTransportService = $SmtpTransportService;
		return $this;
	}
	public function getAuthService() {
		return $this->authService;
	}
	public function setAuthService($authService) {
		$this->authService = $authService;
		return $this;
	}
	public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable($userTable) {
		$this->userTable = $userTable;
		return $this;
	}
		
	public function getCurrencyTable() {
		return $this->currencyTable;
	}
	public function setCurrencyTable(CurrencyTable $currencyTable) {
		$this->currencyTable = $currencyTable;
		return $this;
	}
	
	

}
