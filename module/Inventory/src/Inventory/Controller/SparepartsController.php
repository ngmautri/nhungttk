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
use Zend\View\Model\ViewModel;

use MLA\Paginator;
use Inventory\Model\SparepartPicture;
use Inventory\Model\MLASparepart;
use Inventory\Model\SparepartMovement;
use Inventory\Model\SparepartMovementsTable;

class SparepartsController extends AbstractActionController {
	public $authService;
	public $sparePartService;
	public $userTable;
	public $mlaSparepartTable;
	public $sparepartPictureTable;
	public $sparepartMovementsTable;
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
}
