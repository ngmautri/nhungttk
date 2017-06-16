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
use Application\Entity\NmtInventoryItemEmployee;
use Zend\Math\Rand;

/**
 *
 * @author nmt
 *        
 */
class ItemAssignmentController extends AbstractActionController {
	const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
	protected $doctrineEM;
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
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
		
		$uom = null;
		if (! $item == null) {
			$uom = $item->getStandardUom ();
		}
		
		return new ViewModel ( array (
				'entity' => $item,
				'pictures' => $pictures,
				'back' => $redirectUrl,
				'category' => null,
				'uom' => $uom,
				'department' => null 
		
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
	 */
	public function addAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			$errors = array ();
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$target_id = $request->getPost ( 'target_id' );
			$token = $request->getPost ( 'token' );
			
			$criteria = array (
					'id' => $target_id,
					'token' => $token 
			);
			
			/**
			 *
			 * @todo Update Target
			 */
			$target = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findOneBy ( $criteria );
			
			if ($target == null) {
				
				$errors [] = 'Target object can\'t be empty. Or token key is not valid!';
				$this->flashMessenger ()->addMessage ( 'Something went wrong!' );
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'errors' => $errors,
						'target' => null,
						'entity' => null 
				) );
				
				// might need redirect
			} else {
				
				$employee_id = $request->getPost ( 'employee_id' );
				$remarks = $request->getPost ( 'remarks' );
				$isActive = $request->getPost ( 'isActive' );
				
				if ($isActive !== 1) {
					$isActive = 0;
				}
				
				$entity = new NmtInventoryItemEmployee ();
				$entity->setItem ( $target );
				
				$entity->setIsActive ( $isActive );
				$employee = null;
				if ($employee_id > 0) {
					
					$employee = $this->doctrineEM->find ( 'Application\Entity\NmtHrEmployee', $employee_id );
				}
				
				if ($employee == null) {
					$errors [] = 'Employee can\'t be empty. Please select a employee!';
				} else {
					$entity->setEmployee ($employee);
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
				// OK now
				
				$entity->setRemarks ( $remarks );
				
				$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
						'email' => $this->identity () 
				) );
				
				$entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
				
				$entity->setCreatedBy ( $u );
				$entity->setCreatedOn ( new \DateTime () );
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				$this->doctrineEM->flush ();
				
				$this->flashMessenger ()->addMessage ( "Item has been assigned successfully!" );
				return $this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		// NO POST
		$redirectUrl = Null;
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$target_id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		$token = $this->params ()->fromQuery ( 'token' );
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		
		$criteria = array (
				'id' => $target_id,
				'checksum' => $checksum,
				'token' => $token 
		);
		
		$target = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findOneBy ( $criteria );
		
		if ($target !== null) {
			return new ViewModel ( array (
					'redirectUrl' => $redirectUrl,
					'errors' => null,
					'entity' => null,
					'target' => $target 
			) );
		} else {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
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
		
		$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
				'email' => $this->identity () 
		) );
		
		if ($request->isPost ()) {
			
			$item_id = $request->getPost ( 'item_id' );
			
			$item_sku = $request->getPost ( 'item_sku' );
			$item_name = $request->getPost ( 'item_name' );
			$item_name_foreign = $request->getPost ( 'item_name_foreign' );
			$item_description = $request->getPost ( 'item_description' );
			
			$item_barcode = $request->getPost ( 'item_barcode' );
			$keywords = $request->getPost ( 'keywords' );
			
			$item_type = $request->getPost ( 'item_type' );
			$item_category_id = $request->getPost ( 'item_category_id' );
			$department_id = $request->getPost ( 'department_id' );
			$location = $request->getPost ( 'location' );
			
			$standard_uom_id = $request->getPost ( 'standard_uom_id' );
			$item_leadtime = $request->getPost ( 'lead_time' );
			$local_availability = $request->getPost ( 'local_availability' );
			
			$is_active = $request->getPost ( 'is_active' );
			$is_stocked = $request->getPost ( 'is_stocked' );
			$is_purchased = $request->getPost ( 'is_purchased' );
			$is_sale_item = $request->getPost ( 'is_sale_item' );
			$is_fixed_asset = $request->getPost ( 'is_fixed_asset' );
			
			$manufacturer = $request->getPost ( 'manufacturer' );
			$manufacturer_code = $request->getPost ( 'manufacturer_code' );
			$manufacturer_model = $request->getPost ( 'manufacturer_model' );
			$manufacturer_serial = $request->getPost ( 'manufacturer_serial' );
			$remarks = $request->getPost ( 'remarks' );
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			
			/*
			 * $r = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findBy ( array (
			 * 'itemSku' => $item_sku
			 * ) );
			 *
			 * if (count ( $r ) >= 1) {
			 * $errors [] = $item_sku . ' exists';
			 * }
			 */
			
			$errors = array ();
			
			$entity = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $item_id );
			
			if ($item_sku === '' or $item_sku === null) {
				$errors [] = 'Please give ID';
			}
			$entity->setItemSku ( $item_sku );
			
			if ($item_name === '' or $item_name === null) {
				$errors [] = 'Please give item name';
			}
			$entity->setItemName ( $item_name );
			
			$entity->setItemNameForeign ( $item_name_foreign );
			$entity->setItemDescription ( $item_description );
			$entity->setBarcode ( $item_barcode );
			$entity->setKeywords ( $keywords );
			$entity->setItemType ( $item_type );
			
			if ($standard_uom_id === '' or $standard_uom_id === null) {
				$errors [] = 'Please give standard measurement!';
				$uom = null;
			} else {
				$uom = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationUom', $standard_uom_id );
				$entity->setStandardUom ( $uom );
			}
			
			// category
			$category = null;
			
			if ($item_category_id > 0) {
				$category = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItemCategory', $item_category_id );
			}
			
			// add department member
			$department = null;
			if ($department_id > 0) {
				$department = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationDepartment', $department_id );
			}
			
			$entity->setLocation ( $location );
			
			$entity->setManufacturer ( $manufacturer );
			
			$entity->setManufacturerCode ( $manufacturer_code );
			$entity->setManufacturerModel ( $manufacturer_model );
			$entity->setManufacturerSerial ( $manufacturer_serial );
			
			$entity->setIsActive ( $is_active );
			$entity->setIsStocked ( $is_stocked );
			$entity->setIsPurchased ( $is_purchased );
			$entity->setIsSaleItem ( $is_sale_item );
			$entity->setIsFixedAsset ( $is_fixed_asset );
			
			$entity->setLeadTime ( $item_leadtime );
			$entity->setLocalAvailabiliy ( $local_availability );
			$entity->setRemarks ( $remarks );
			
			$company_id = 1;
			$company = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCompany', $company_id );
			$entity->setCompany ( $company );
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'errors' => $errors,
						'redirectUrl' => $redirectUrl,
						'entity' => $entity,
						'category' => $category,
						'uom' => $uom,
						'department' => $department,
						'item_id' => $item_id 
				
				) );
			}
			
			// No Error
			try {
				
				$entity->setLastChangeOn ( new \DateTime () );
				$entity->setLastChangeBy ( $u );
				$this->doctrineEM->flush ();
				
				$new_item = $entity;
				
				// add category member
				if ($item_category_id > 0) {
					$entity = new NmtInventoryItemCategoryMember ();
					$entity->setItem ( $new_item );
					$entity->setCategory ( $category );
					
					$entity->setCreatedBy ( $u );
					$entity->setCreatedOn ( new \DateTime () );
					
					$this->doctrineEM->persist ( $entity );
					$this->doctrineEM->flush ();
				}
				
				// add department member
				if ($department_id > 0) {
					$entity = new NmtInventoryItemDepartment ();
					$entity->setDepartment ( $department );
					$entity->setItem ( $new_item );
					
					$entity->setCreatedBy ( $u );
					$entity->setCreatedOn ( new \DateTime () );
					
					$this->doctrineEM->persist ( $entity );
					$this->doctrineEM->flush ();
				}
				
				// update search index.
				// $this->itemSearchService->addDocument ( $new_item, true );
			} catch ( Exception $e ) {
				return new ViewModel ( array (
						'errors' => $e->getMessage (),
						'redirectUrl' => $redirectUrl,
						'entity' => null,
						'category' => null,
						'uom' => null,
						'department' => null,
						'item_id' => $item_id 
				
				) );
			}
			
			$this->flashMessenger ()->addSuccessMessage ( "Item " . $item_name . " has been updated sucessfully" );
			return $this->redirect ()->toUrl ( $redirectUrl );
		}
		
		// Not Post
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$item_id = ( int ) $this->params ()->fromQuery ( 'item_id' );
		$item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $item_id );
		$uom = $item->getStandardUom ();
		/*
		 * $pictures = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPicture' )->findBy ( array (
		 * "item" => $item_id
		 * ) );
		 */
		
		return new ViewModel ( array (
				'errors' => null,
				'redirectUrl' => $redirectUrl,
				'entity' => $item,
				'category' => null,
				'uom' => $uom,
				'department' => null,
				'item_id' => $item_id 
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
		
		$target_id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		$token = $this->params ()->fromQuery ( 'token' );
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		
		$criteria = array (
				'id' => $target_id,
				'checksum' => $checksum,
				'token' => $token 
		);
		
		$target = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findOneBy ( $criteria );
		
		if ($target == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$criteria = array (
				'item' => $target_id 
		);
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemEmployee' )->findBy ( $criteria );
		$total_records = count ( $list );
		$paginator = null;
		
		// $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
		// var_dump (count($all));
		
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => $paginator,
				'target' => $target 
		
		) );
	}
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
}
