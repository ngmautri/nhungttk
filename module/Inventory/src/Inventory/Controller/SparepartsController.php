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

class SparepartsController extends AbstractActionController {
	protected $authService;
	protected $SmtpTransportService;
	protected $sparePartService;
	protected $userTable;
	protected $sparePartTable;
	protected $sparePartPictureTable;
	protected $sparepartMovementsTable;
	protected $sparePartCategoryTable;
	protected $sparePartCategoryMemberTable;
	protected $massage = 'NULL';
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 * create new spare part
	 */
	public function addAction() {
		
		$request = $this->getRequest ();
			
		if ($request->isPost ()) {
			
			$request = $this->getRequest ();
			
			
			if ($request->isPost ()) {
				$redirectUrl  = $request->getPost ( 'redirectUrl' );
				
				$input = new MLASparepart ();
				$input->name = $request->getPost ( 'name' );
				$input->name_local = $request->getPost ( 'name_local' );
				
				$input->description = $request->getPost ( 'description' );
				$input->code = $request->getPost ( 'code' );
				$input->tag = $request->getPost ( 'tag' );
				
				$input->location = $request->getPost ( 'location' );
				$input->comment = $request->getPost ( 'comment' );
				
				$category_id = (int) $request->getPost ( 'category_id' );
				
								
				$errors = array();
				
				//tag must be unique
				
				if ($this->sparePartTable->isTagExits($input->tag) === true){
					$errors [] = 'Sparepart with tag number "'. $input->tag.  '" exits already in database';					
				}
				

				if ($input->name ==''){
					$errors [] = 'Please give spare-part name';
				}
					
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							'errors' => $errors,
							'redirectUrl'=>$redirectUrl,
							'category_id' => $category_id,
							'sparepart' =>$input,							
					) );
				}
				
				
				$newId = $this->sparePartTable->add ( $input );
				$root_dir = $this->sparePartService->createSparepartFolderById ( $newId );
				$pictures_dir = $root_dir . DIRECTORY_SEPARATOR . "pictures";
				
				// $files = $request->getFiles ()->toArray ();
				
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
						$pic = new SparepartPicture ();
						$pic->url = "$pictures_dir/$name";
						$pic->filetype = $ftype;
						$pic->sparepart_id = $input->id;
						$pic->filename = $name;
						$pic->folder = $pictures_dir;					
						$this->sparePartPictureTable->add ( $pic);
					
						//create thumbnail
						
						// trigger uploadPicture
						$this->getEventManager()->trigger(
								'uploadPicture', __CLASS__, array('picture_name' => $name,'pictures_dir'=>$pictures_dir)
								);
						
					}
				}
				
				// add category
				if ($category_id > 1){
					$m = new SparepartCategoryMember();
					$m->sparepart_id = $newId;
					$m->sparepart_cat_id = $category_id;						
					$this->sparePartCategoryMemberTable->add($m);					
				}
								
				$this->redirect()->toUrl($redirectUrl);
			}
		}
		
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		$category_id = ( int ) $this->params ()->fromQuery ( 'category_id' );
			
		return new ViewModel ( array (
				'message' => 'Add new Sparepart',
				'category_id' => $category_id,
				'redirectUrl'=>$redirectUrl,
				'errors' => null,
				'sparepart' =>null,
			) );
	}
	
	/**
	 * Edit Spare part
	 * @return \Zend\View\Model\ViewModel
	 */
	
	public function editAction() {
		$request = $this->getRequest ();
			
	
		if ($request->isPost ()) {
				
			$input = new MLASparepart ();
			$input->id = $request->getPost ( 'id' );
			$input->name = $request->getPost ( 'name' );
			$input->name_local = $request->getPost ( 'name_local' );
				
			$input->description = $request->getPost ( 'description' );
			$input->code = $request->getPost ( 'code' );
			$input->tag = $request->getPost ( 'tag' );
				
			$input->location = $request->getPost ( 'location' );
			$input->comment = $request->getPost ( 'comment' );
			

			$errors = array();
			
			if ($input->name ==''){
				$errors [] = 'Please give spare-part name';
			}
			
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
			
			
			if (count ( $errors ) > 0) {
				// return current sp
				$sparepart = $this->sparePartTable->get ( $input->id );
				
				return new ViewModel ( array (
						'errors' => $errors,
						'redirectUrl'=>$redirectUrl,
						'sparepart' =>$sparepart,
				) );
			}
			
			
				
			$this->sparePartTable->update ( $input, $input->id );
			$root_dir = $this->sparePartService->getSparepartPath ( $input->id );
			$pictures_dir = $root_dir . DIRECTORY_SEPARATOR . "pictures";
				
			// $files = $request->getFiles ()->toArray ();
				
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
					$pic = new SparepartPicture ();
					$pic->url = "$pictures_dir/$name";
					$pic->filetype = $ftype;
					$pic->sparepart_id = $input->id;
					$pic->filename = "$name";
					$pic->folder = "$pictures_dir";
					$this->sparePartPictureTable->add ( $pic);
						
					// trigger uploadPicture
					$this->getEventManager()->trigger(
							'uploadPicture', __CLASS__, array('picture_name' => $name,'pictures_dir'=>$pictures_dir)
							);
	
				}
			}
				
			$this->redirect()->toUrl($redirectUrl);
				
			//return $this->redirect ()->toRoute ( 'Spareparts_Category');
		}
	
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$sparepart = $this->sparePartTable->get ( $id );
	
		return new ViewModel ( array (
				'sparepart' => $sparepart,
				'redirectUrl'=>$redirectUrl,
				'errors' => null,
		) );
	}
	
	/**
	 * Edit Spare part
	 * @return \Zend\View\Model\ViewModel
	 */
	
	public function uploadPictureAction() {
		$request = $this->getRequest ();
			
	
		if ($request->isPost ()) {
	
			$id = $request->getPost ( 'id' );
			$root_dir = $this->sparePartService->getSparepartPath ($id );
			$pictures_dir = $root_dir . DIRECTORY_SEPARATOR . "pictures";
	
			// $files = $request->getFiles ()->toArray ();
	
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
					$pic = new SparepartPicture ();
					$pic->url = "$pictures_dir/$name";
					$pic->filetype = $ftype;
					$pic->sparepart_id = $id;
					$pic->filename = "$name";
					$pic->folder = "$pictures_dir";
					$this->sparePartPictureTable->add ( $pic);
	
					// trigger uploadPicture
					$this->getEventManager()->trigger(
							'uploadPicture', __CLASS__, array('picture_name' => $name,'pictures_dir'=>$pictures_dir)
							);
	
				}
			}
	
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
			$this->redirect()->toUrl($redirectUrl);
	
			//return $this->redirect ()->toRoute ( 'Spareparts_Category');
		}
	
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$sparepart = $this->sparePartTable->get ( $id );
	
		return new ViewModel ( array (
				'sparepart' => $sparepart,
				'redirectUrl'=>$redirectUrl,
				'errors' => null,
		) );
	}
	
	/**
	 * Issue sparepart
	 */
	public function issueAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
	
	
		if ($request->isPost ()) {
				
			$input = new SparepartMovement ();
			$input->sparepart_id = $request->getPost ( 'sparepart_id' );
			$input->movement_date = $request->getPost ( 'movement_date' );
				
			$input->sparepart_id = $request->getPost ( 'sparepart_id' );
			$input->asset_id = $request->getPost ( 'asset_id' );
			$input->asset_name = $request->getPost ( 'asset' );
				
			$input->quantity = $request->getPost ( 'quantity' );
				
			$input->flow = 'OUT';
				
			$input->reason = $request->getPost ( 'reason' );
			$input->requester = $request->getPost ( 'requester' );
			$input->comment = $request->getPost ( 'comment' );
			$input->created_on = $request->getPost ( 'created_on' );
				
			$instock = $request->getPost ( 'instock' );
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
			
		
				
			// validator.
			$validator = new Date ();
				
			$errors = array();
	
				
			if (! $validator->isValid ( $input->movement_date )) {
				$errors [] = 'Transaction date format is not correct!';
			}
				
			// Fixed it by going to php.ini and uncommenting extension=php_intl.dll
			$validator = new Int ();
				
			if (! $validator->isValid ( $input->quantity )) {
				$errors [] = 'Quantity is not valid. It must be a number.';
			} else {
	
				if ($input->quantity > $instock) {
					$errors [] = 'Issue quantity is: ' . $input->quantity . ' pcs, which is bigger than availabe stock';
				}
			}
				
			if (count ( $errors ) > 0) {
	
				$id = ( int ) $request->getPost ( 'sparepart_id' );
				$sp = $this->sparePartTable->get ( $id );
				$pictures = $this->sparePartPictureTable->getSparepartPicturesById ( $id );
	
				return new ViewModel ( array (
						'sp' => $sp,
						'pictures' => $pictures,
						'instock' => $instock,
						'errors' => $errors,
						'redirectUrl'=>$redirectUrl,
						'movement'=>$input,
				) );
			} else {
	
				// Validated
				$this->sparepartMovementsTable->add ( $input );
	
				$this->redirect()->toUrl($redirectUrl);
			}
		}
	
		$id = ( int ) $this->params ()->fromQuery ( 'sparepart_id' );
		$sp = $this->sparePartTable->get ( $id );
		$pictures = $this->sparePartPictureTable->getSparepartPicturesById ( $id );
		$inflow = $this->sparepartMovementsTable->getTotalInflowOf ( $id );
		$outflow = $this->sparepartMovementsTable->getTotalOutflowOf ( $id );
		$instock = $inflow - $outflow;
	
		return new ViewModel ( array (
				'sp' => $sp,
				'pictures' => $pictures,
				'instock' => $instock,
				'errors' => null,
				'redirectUrl'=>$redirectUrl,
				'movement'=>null,
			) );
	}
	
	/**
	 * receive spare part
	 */
	public function receiveAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
	
	
		if ($request->isPost ()) {
				
			$request = $this->getRequest ();
				
			if ($request->isPost ()) {
	
				$input = new SparepartMovement ();
				$input->sparepart_id = $request->getPost ( 'sparepart_id' );
				$input->movement_date = $request->getPost ( 'movement_date' );
	
				$input->sparepart_id = $request->getPost ( 'sparepart_id' );
				$input->asset_id = $request->getPost ( 'asset_id' );
				$input->quantity = $request->getPost ( 'quantity' );
	
				$input->flow = 'IN';
	
				$input->reason = $request->getPost ( 'reason' );
				$input->requester = $request->getPost ( 'requester' );
				$input->comment = $request->getPost ( 'comment' );
				$input->created_on = $request->getPost ( 'created_on' );
				$email = $request->getPost ( 'email' );
	
				$instock = $request->getPost ( 'instock' );
				$redirectUrl  = $request->getPost ( 'redirectUrl' );
				
	
				$errors = array();
	
	
				// validator.
				$validator = new Date ();
	
				if (! $validator->isValid ( $input->movement_date )) {
					$errors [] = 'Transaction date format is not correct!';
				}
	
				// Fixed it by going to php.ini and uncommenting extension=php_intl.dll
				$validator = new Int ();
	
				if (! $validator->isValid ( $input->quantity )) {
					$errors [] = 'Quantity is not valid. It must be a number.';
				}
	
				$validator = new EmailAddress ();
				if (! $validator->isValid ( $email )) {
					$errors [] = 'Email is not correct.';
				}
	
				$id = ( int ) $request->getPost ( 'sparepart_id' );
				$sp = $this->sparePartTable->get ( $id );
				$pictures = $this->sparePartPictureTable->getSparepartPicturesById ( $id );
	
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							'sp' => $sp,
							'pictures' => $pictures,
							'instock' => $instock,
							'errors' => $errors,
							'redirectUrl'=>$redirectUrl,								
							'movement'=>$input,
								
					) );
				} else {
						
					// Validated
					$newId = $this->sparepartMovementsTable->add ( $input );
						
					if ($newId > 0) {
						// sent email;
	
						$transport = $this->getServiceLocator ()->get ( 'SmtpTransportService' );
						$message = new Message ();
						$body = $input->quantity . ' pcs of Spare parts ' . $sp->name . ' (ID' . $sp->tag . ') received!';
						$message->addTo ( $email )->addFrom ( 'mib-team@web.de' )->setSubject ( 'Mascot Laos - Spare Part Movements' )->setBody ( $body );
						$transport->send ( $message );
					}
						
						
					$redirectUrl  = $request->getPost ( 'redirectUrl' );
					$this->redirect()->toUrl($redirectUrl);
				}
			}
		}
	
		$id = ( int ) $this->params ()->fromQuery ( 'sparepart_id' );
		$sp = $this->sparePartTable->get ( $id );
		$pictures = $this->sparePartPictureTable->getSparepartPicturesById ( $id );
		$inflow = $this->sparepartMovementsTable->getTotalInflowOf ( $id );
		$outflow = $this->sparepartMovementsTable->getTotalOutflowOf ( $id );
		$instock = $inflow - $outflow;
	
		return new ViewModel ( array (
				'sp' => $sp,
				'pictures' => $pictures,
				'instock' => $instock,
				'errors' => null,
				'redirectUrl'=>$redirectUrl,
				'movement'=>null,
		) );
	}
	
	public function addCategoryAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
		
		if ($request->isPost ()) {
			
			$sparepart_id = ( int ) $request->getPost ( 'id' );
			$categories = $request->getPost ( 'category' );
			
			if (count ( $categories ) > 0) {
				
				foreach ( $categories as $cat ) {
					$member = new SparepartCategoryMember ();
					$member->sparepart_cat_id = $cat;
					$member->sparepart_id = $sparepart_id;
					
					if ($this->sparePartCategoryMemberTable->isMember ( $sparepart_id, $cat ) == false) {
						$this->sparePartCategoryMemberTable->add ( $member );
					}
				}
				
				/*
				 * return new ViewModel ( array (
				 * 'sparepart' => null,
				 * 'categories' => $categories,
				 *
				 * ) );
				 */
				$redirectUrl  = $request->getPost ( 'redirectUrl' );
				$this->redirect()->toUrl($redirectUrl);
	
			}
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$sparepart = $this->sparePartTable->get ( $id );
		
		$categories = $this->sparePartCategoryTable->getCategories();
		
		return new ViewModel ( array (
				'sparepart' => $sparepart,
				'categories' => $categories,
				'redirectUrl'=>$redirectUrl,
		)
		
		);
	}
	
	/**
	 * List all spare parts
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
		
		$spareparts = $this->sparePartTable->getSpareparts();
		$totalResults = $spareparts->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$spareparts = $this->sparePartTable->getLimitedSpareParts ( ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'total_spareparts' => $totalResults,
				'spareparts' => $spareparts,
				'paginator' => $paginator 
		) );
	}
	
	
	/**
	 * List all spare parts
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
		
		$this->layout('layout/no-layout');
		
	
		return new ViewModel ( array (
				'total_spareparts' => $totalResults,
				'spareparts' => $spareparts,
				'paginator' => $paginator
		) );
	}
	
	
	/*
	 * * 
	 */
	public function categoryAction() {
		$categories = $this->sparePartCategoryTable->getCategories1();
		
		return new ViewModel ( array (
				'assetCategories' =>$categories,
		));
		
		
	
	}
	
	/*
	 * *
	 */
	public function category1Action() {
	
		$categories = $this->sparePartCategoryTable->getCategories1();
		$this->layout('layout/no-layout');
		
		
		$viewModel = new ViewModel(array (
				'assetCategories' =>$categories,
		));		
		
		return $viewModel;
	}
	
	public function showCategoryAction() {
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
		
		$category = $this->sparePartCategoryTable->get ( $id );
		
		$spareparts = $this->sparePartCategoryMemberTable->getMembersByCatIdWithBalance( $id );
		$totalResults = $spareparts->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$spareparts = $this->sparePartCategoryMemberTable->getLimitMembersByCatIdWithBalance ($id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		return new ViewModel ( array (
				'category' => $category,
				'total_spareparts' => $totalResults,
				'spareparts' => $spareparts,
				'paginator' => $paginator 
		) );
	}
	
	/**
	 * Show detail of a spare parts
	 */
	public function showAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$sp = $this->sparePartTable->get ( $id );		
		$pictures = $this->sparePartPictureTable->getSparepartPicturesById ( $id );
		$inflow = $this->sparepartMovementsTable->getTotalInflowOf ( $id );
		$outflow = $this->sparepartMovementsTable->getTotalOutflowOf ( $id );
		$categories = $this->sparePartCategoryTable->getCategoriesOf($id);
		
		$instock = $inflow - $outflow;
		
		return new ViewModel ( array (
				'sparepart' => $sp,
				'pictures' => $pictures,
				'inflow' => $inflow,
				'outflow' => $outflow,
				'instock' => $instock,
				'categories' =>$categories
		) );
	}
	
	
	public function picturesAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'sparepart_id' );
		$sp = $this->sparePartTable->get ( $id );
		$pictures = $this->sparePartPictureTable->getSparepartPicturesById ( $id );

		return new ViewModel ( array (
				'sparepart' => $sp,
				'pictures' => $pictures,
			) );
	}
	
	
	
	
	
	

	
	
	public function showMovementAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'sparepart_id' );
		$movements = $this->getSparepartMovementsTable ()->getMovementsOfSparepartByID ( $id );
		
		$sp = $this->sparePartTable->get ( $id );
		$pictures = $this->sparePartPictureTable->getSparepartPicturesById ( $id );
		
		return new ViewModel ( array (
				'sparepart' => $sp,
				'movements' => $movements,
				'pictures' => $pictures 
		) );
	}
	
	// SETTER AND GETTER
	public function getAuthService() {
		return $this->authService;
	}
	public function setAuthService($authService) {
		$this->authService = $authService;
		return $this;
	}
	public function setSmtpTransportService($SmtpTransportService) {
		$this->SmtpTransportService = $SmtpTransportService;
		return $this;
	}
	public function setSparePartService(SparepartService $sparePartService) {
		$this->sparePartService = $sparePartService;
		return $this;
	}
	public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable($userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getSparePartTable() {
		return $this->sparePartTable;
	}
	public function setSparePartTable(MLASparepartTable $sparePartTable) {
		$this->sparePartTable = $sparePartTable;
		return $this;
	}
	public function setSparepartMovementsTable(SparepartMovementsTable $sparepartMovementsTable) {
		$this->sparepartMovementsTable = $sparepartMovementsTable;
		return $this;
	}
	public function setSparePartCategoryTable(SparepartCategoryTable $sparePartCategoryTable) {
		$this->sparePartCategoryTable = $sparePartCategoryTable;
		return $this;
	}
	public function setSparePartPictureTable(SparepartPictureTable $sparePartPictureTable) {
		$this->sparePartPictureTable = $sparePartPictureTable;
		return $this;
	}
	public function setSparePartCategoryMemberTable(SparepartCategoryMemberTable $sparePartCategoryMemberTable) {
		$this->sparePartCategoryMemberTable = $sparePartCategoryMemberTable;
		return $this;
	}
	public function getSparePartService() {
		return $this->sparePartService;
	}
	public function getSmtpTransportService() {
		return $this->SmtpTransportService;
	}
	public function getSparePartPictureTable() {
		return $this->sparePartPictureTable;
	}
	public function getSparepartMovementsTable() {
		return $this->sparepartMovementsTable;
	}
	public function getSparePartCategoryTable() {
		return $this->sparePartCategoryTable;
	}
	public function getSparePartCategoryMemberTable() {
		return $this->sparePartCategoryMemberTable;
	}
}
