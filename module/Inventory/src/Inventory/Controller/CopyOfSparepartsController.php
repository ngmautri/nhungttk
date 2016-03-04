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
use Zend\Validator\Date;
use Zend\I18n\Validator\Int;
use Zend\Validator\EmailAddress;

use Zend\Mail\Message;

use Zend\View\Model\ViewModel;

use MLA\Paginator;
use Inventory\Model\SparepartPicture;
use Inventory\Model\MLASparepart;
use Inventory\Model\SparepartMovement;
use Inventory\Model\SparepartMovementsTable;
use Inventory\Model\SparepartCategoryMember;

class SparepartsController1 extends AbstractActionController {
	public $authService;
	public $SmtpTransportService;
	
	public $sparePartService;
	public $userTable;
	public $mlaSparepartTable;
	public $sparepartPictureTable;
	public $sparepartMovementsTable;
	
	protected $sparePartCategoryTable;
	protected $sparePartCategoryMemberTable;
	
	
	public $massage = 'NULL';
	public $errors = array ();
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 * Add new spare part
	 */
	public function addAction() {
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			
			$request = $this->getRequest ();
			if ($request->isPost ()) {
				
				$input = new MLASparepart ();
				$input->name = $request->getPost ( 'name' );
				$input->name_local = $request->getPost ( 'name_local' );
				
				$input->description = $request->getPost ( 'description' );
				$input->code = $request->getPost ( 'category_id' );
				$input->tag = $request->getPost ( 'group_id' );
				
				$input->location = $request->getPost ( 'tag' );
				$input->comment = $request->getPost ( 'location' );
				
				$newId = $this->getMLASparepartTable ()->add ( $input );
				$root_dir = $this->getSparepartService ()->createSparepartFolderById ( $newId );
				$pictures_dir = $root_dir . DIRECTORY_SEPARATOR . "pictures";
				
				$files = $request->getFiles ()->toArray ();
				
				foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
					if ($error == UPLOAD_ERR_OK) {
						$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
						$name = $_FILES ["pictures"] ["name"] [$key];
						$ftype = $_FILES ["pictures"] ["type"] [$key];
						move_uploaded_file ( $tmp_name, "$pictures_dir/$name" );
						
						// add pictures
						$pic = new SparepartPicture ();
						$pic->url = "$pictures_dir/$name";
						$pic->filetype = $ftype;
						$pic->spare_id = $newId;
						$this->getSparepartPictureTable ()->add ( $pic );
					}
				}
				return $this->redirect ()->toRoute ( 'assetcategory' );
			}
		}
		
		return new ViewModel ( array (
				'message' => 'Add new Sparepart' 
		) );
	}
	
	public function addCategoryAction() {
		
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$sparepart_id = (int) $request->getPost ( 'id' );
			$categories = $request->getPost ( 'category' );
			
			if (count($categories) > 0) {
				
				foreach ($categories as $cat){
					$member = new SparepartCategoryMember();
					$member->sparepart_cat_id = $cat;
					$member->sparepart_id =  $sparepart_id;
					
					if ($this->getSparePartCategoryMemberTable()->isMember($sparepart_id,$cat) == false){
						$this->getSparePartCategoryMemberTable()->add($member);
					}				
				 	
				}
				
				/*				
				return new ViewModel ( array (
						'sparepart' => null,
						'categories' => $categories,
							
				) );
				*/	
				return $this->redirect ()->toRoute ( 'spare_parts_list' );
			}
		}
	
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$sparepart = $this->getMLASparepartTable ()->get ( $id );
		
		$categories = $this->getSparePartCategoryTable()->fetchAll ();
		
		return new ViewModel ( array (
				'sparepart' => $sparepart,
				'categories' => $categories,
				
		) );
	}
	
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
			
			$this->getMLASparepartTable ()->update ( $input, $input->id );
			$root_dir = $this->getSparepartService ()->getSparepartPath ( $input->id );
			$pictures_dir = $root_dir . DIRECTORY_SEPARATOR . "pictures";
			
			$files = $request->getFiles ()->toArray ();
			
			foreach ( $_FILES ["pictures"] ["error"] as $key => $error ) {
				if ($error == UPLOAD_ERR_OK) {
					$tmp_name = $_FILES ["pictures"] ["tmp_name"] [$key];
					$name = $_FILES ["pictures"] ["name"] [$key];
					$ftype = $_FILES ["pictures"] ["type"] [$key];
					move_uploaded_file ( $tmp_name, "$pictures_dir/$name" );
					
					// add pictures
					$pic = new SparepartPicture ();
					$pic->url = "$pictures_dir/$name";
					$pic->filetype = $ftype;
					$pic->sparepart_id = $input->id;
					$this->getSparepartPictureTable ()->add ( $pic );
				}
			}
			
			return $this->redirect ()->toRoute ( 'assetcategory' );
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$sparepart = $this->getMLASparepartTable ()->get ( $id );
		
		return new ViewModel ( array (
				'sparepart' => $sparepart 
		) );
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
		
		$spareparts = $this->getMLASparepartTable ()->fetchAll ();
		$totalResults = $spareparts->count ();
		
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$spareparts = $this->getMLASparepartTable ()->getLimitSpareParts ( ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
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
		
		$category = $this->getSparePartCategoryTable()->get($id);
	
		$spareparts = $this->getSparePartCategoryMemberTable()->getMembersByCatId ($id);
		$totalResults = $spareparts->count ();
	
		$paginator = null;
		if ($totalResults > $resultsPerPage) {
			$paginator = new Paginator ( $totalResults, $page, $resultsPerPage );
			$spareparts = $this->getMLASparepartTable ()->getLimitSpareParts ( ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
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
		$request = $this->getRequest ();
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$sp = $this->getMLASparepartTable ()->get ( $id );
		
		$pictures = $this->getSparepartPictureTable ()->getSparepartPicturesById ( $id );
		$inflow = $this->getSparepartMovementsTable ()->getTotalInflowOf ( $id );
		$outflow = $this->getSparepartMovementsTable ()->getTotalOutflowOf ( $id );
		$instock = $inflow - $outflow;
		
		return new ViewModel ( array (
				'sparepart' => $sp,
				'pictures' => $pictures,
				'inflow' => $inflow,
				'outflow' => $outflow,
				'instock' => $instock,
		) );
	}
	
	/**
	 * receive spare part
	 */
	public function receiveAction() {
		$request = $this->getRequest ();
		
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
				
				
				// validator.
				$validator = new Date ();
					
				if (! $validator->isValid ( $input->movement_date )) {
					$errors [] = 'Transaction date format is not correct!';
				}
				
				//Fixed it by going to php.ini and uncommenting extension=php_intl.dll
				$validator = new Int();
					
				if (! $validator->isValid ( $input->quantity )) {
					$errors [] = 'Quantity is not valid. It must be a number.';
				}
				
				$validator = new EmailAddress();
				if (! $validator->isValid ( $email )) {
					$errors [] = 'Email is not correct.';
				}
				
				$id = ( int ) $request->getPost ( 'sparepart_id' );
				$sp = $this->getMLASparepartTable ()->get ( $id );
				$pictures = $this->getSparepartPictureTable ()->getSparepartPicturesById ( $id );
				
					
				if (count($errors) > 0) {
					return new ViewModel ( array (
							'sp' => $sp,
							'pictures' => $pictures,
							'instock' => $instock,
							'errors' => $errors,
					) );
				} else {
				
					// Validated
					$newId = $this->getSparepartMovementsTable ()->add ( $input );
					
					if($newId >0){
						// sent email;
						
						 $transport = $this->getServiceLocator()->get('SmtpTransportService');
						 $message = new Message ();
						 $body = $input->quantity . ' pcs of Spare parts ' . $sp->name . ' (ID'.  $sp->tag. ') received!';
						 $message->addTo ( $email )->addFrom ( 'mib-team@web.de' )->setSubject ( 'Mascot Laos - Spare Part Movements' )->setBody ($body);
						 $transport->send ( $message );
						
					}
					
					
					return $this->redirect ()->toRoute ( 'assetcategory' );
				}		
				
				
				$newId = $this->getSparepartMovementsTable ()->add ( $input );
				return $this->redirect ()->toRoute ( 'assetcategory' );
			}
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'sparepart_id' );
		$sp = $this->getMLASparepartTable ()->get ( $id );
		$pictures = $this->getSparepartPictureTable ()->getSparepartPicturesById ( $id );
		$inflow = $this->getSparepartMovementsTable ()->getTotalInflowOf ( $id );
		$outflow = $this->getSparepartMovementsTable ()->getTotalOutflowOf ( $id );
		$instock = $inflow - $outflow;
		
		return new ViewModel ( array (
				'sp' => $sp,
				'pictures' => $pictures,
				'instock' => $instock,
				'errors' => null,
		) );
	}
	
	public function issueAction() {
		
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$input = new SparepartMovement ();
			$input->sparepart_id = $request->getPost ( 'sparepart_id' );
			$input->movement_date = $request->getPost ( 'movement_date' );
			
			$input->sparepart_id = $request->getPost ( 'sparepart_id' );
			$input->asset_id = $request->getPost ( 'asset_id' );
			$input->quantity = $request->getPost ( 'quantity' );
			
			$input->flow = 'OUT';
			
			$input->reason = $request->getPost ( 'reason' );
			$input->requester = $request->getPost ( 'requester' );
			$input->comment = $request->getPost ( 'comment' );
			$input->created_on = $request->getPost ( 'created_on' );
			
			$instock = $request->getPost ( 'instock' );
			
			// validator.
			$validator = new Date ();
			
			if (! $validator->isValid ( $input->movement_date )) {
				$errors [] = 'Transaction date format is not correct!';
			}
			
			
			//Fixed it by going to php.ini and uncommenting extension=php_intl.dll
			$validator = new Int();
			
			if (! $validator->isValid ( $input->quantity )) {
				$errors [] = 'Quantity is not valid. It must be a number.';
			} else {
				
				if ($input->quantity > $instock) {
					$errors [] = 'Issue quantity is: ' . $input->quantity . ' pcs, which is bigger than availabe stock';
				}
			}
			
			if (count($errors) > 0) {
				
				$id = ( int ) $request->getPost ( 'sparepart_id' );
				$sp = $this->getMLASparepartTable ()->get ( $id );
				$pictures = $this->getSparepartPictureTable ()->getSparepartPicturesById ( $id );
				
				return new ViewModel ( array (
						'sp' => $sp,
						'pictures' => $pictures,
						'instock' => $instock,
						'errors' => $errors,
				) );
			} else {
				
				// Validated
				$newId = $this->getSparepartMovementsTable ()->add ( $input );
				return $this->redirect ()->toRoute ( 'assetcategory' );
			}
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'sparepart_id' );
		$sp = $this->getMLASparepartTable ()->get ( $id );
		$pictures = $this->getSparepartPictureTable ()->getSparepartPicturesById ( $id );
		$inflow = $this->getSparepartMovementsTable ()->getTotalInflowOf ( $id );
		$outflow = $this->getSparepartMovementsTable ()->getTotalOutflowOf ( $id );
		$instock = $inflow - $outflow;
		
		return new ViewModel ( array (
				'sp' => $sp,
				'pictures' => $pictures,
				'instock' => $instock,
				'errors' => null,
		) );
	}
	
	
	public function showmovementAction() {
		$request = $this->getRequest ();
		
		$id = ( int ) $this->params ()->fromQuery ( 'sparepart_id' );
		$movements = $this->getSparepartMovementsTable ()->getMovementsOfSparepartByID ( $id );
		
		$sp = $this->getMLASparepartTable ()->get ( $id );
		$pictures = $this->getSparepartPictureTable ()->getSparepartPicturesById ( $id );
		
		return new ViewModel ( array (
				'sparepart' => $sp,
				'movements' => $movements,
				'pictures' => $pictures 
		) );
	}
	
	// get AssetService
	private function getSparepartMovementsTable() {
		if (! $this->sparepartMovementsTable) {
			$sm = $this->getServiceLocator ();
			$this->getSparepartMovementsTable = $sm->get ( 'Inventory\Model\SparepartMovementsTable' );
		}
		return $this->getSparepartMovementsTable;
	}
	
	// get AssetService
	private function getSparepartPictureTable() {
		if (! $this->sparepartPictureTable) {
			$sm = $this->getServiceLocator ();
			$this->sparepartPictureTable = $sm->get ( 'Inventory\Model\SparepartPictureTable' );
		}
		return $this->sparepartPictureTable;
	}
	
	// get AssetService
	private function getMLASparepartTable() {
		if (! $this->mlaSparepartTable) {
			$sm = $this->getServiceLocator ();
			$this->mlaSparepartTable = $sm->get ( 'Inventory\Model\MLASparepartTable' );
		}
		return $this->mlaSparepartTable;
	}
	
	// get AssetService
	private function getSparepartService() {
		if (! $this->sparePartService) {
			$sm = $this->getServiceLocator ();
			$this->sparePartService = $sm->get ( 'Inventory\Services\SparePartService' );
		}
		return $this->sparePartService;
	}
	
	// SmtpTransportService
	private function getSmtpTransportService() {
		if (! $this->SmtpTransportService) {
			$sm = $this->getServiceLocator ();
			$this->SmtpTransportService = $sm->get ( 'SmtpTransportService' );
		}
		return $this->SmtpTransportService;
	}
	
	// table
	private function getSparePartCategoryTable() {
		if (! $this->sparePartCategoryTable) {
			$sm = $this->getServiceLocator ();
			$this->sparePartCategoryTable = $sm->get ( 'Inventory\Model\SparepartCategoryTable' );
		}
		return $this->sparePartCategoryTable;
	}
	
	// table
	private function getSparePartCategoryMemberTable() {
		if (! $this->sparePartCategoryMemberTable) {
			$sm = $this->getServiceLocator ();
			$this->sparePartCategoryMemberTable = $sm->get ( 'Inventory\Model\SparepartCategoryMemberTable' );
		}
		return $this->sparePartCategoryMemberTable;
	}
}
