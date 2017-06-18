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
use Zend\Validator\Date;

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
				$isActive = ( int ) $request->getPost ( 'isActive' );
				$assignedOn = $request->getPost ( 'assignedOn' );
				
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
					$entity->setEmployee ( $employee );
				}
				
				$validator = new Date ();
				if (! $validator->isValid ( $assignedOn )) {
					$errors [] = 'Assignment date is not correct or empty!';
					$entity->setAssignedOn ( null );
				} else {
					$entity->setAssignedOn ( new \DateTime ( $assignedOn ) );
				}
				
				$entity->setRemarks ( $remarks );
				
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
				
				
				$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
						'email' => $this->identity () 
				) );
				
				$entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
				
				$entity->setCreatedBy ( $u );
				$entity->setCreatedOn ( new \DateTime () );
				$this->doctrineEM->persist ( $entity );
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
		
		if ($request->isPost ()) {
				
			$errors = array ();
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$entity_id = ( int ) $request->getPost ( 'entity_id' );
			$token = $request->getPost ( 'token' );
			
			$criteria = array (
					'id' => $entity_id,
					'token' => $token 
			);
			
			$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemEmployee' )->findOneBy ( $criteria );
			
			if ($entity == null) {
				
				$errors [] = 'Entity object can\'t be empty!';
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'errors' => $errors,
						'target' => null,
						'entity' => null 
				) );
			} else {
				
				$employee_id = $request->getPost ( 'employee_id' );
				$remarks = $request->getPost ( 'remarks' );
				$isActive = ( int ) $request->getPost ( 'isActive' );
				$assignedOn = $request->getPost ( 'assignedOn' );
				
				if ($isActive !== 1) {
					$isActive = 0;
				}
				
				//$entity = new NmtInventoryItemEmployee ();
				//$entity->setItem ( $target );
			
				$entity->setIsActive ( $isActive );
				
				//$employee = null;
				if ($employee_id !== $entity->getEmployee()->getId()) {
					$employee = $this->doctrineEM->find ( 'Application\Entity\NmtHrEmployee', $employee_id );					
					if ($employee == null) {
						$errors [] = 'Employee can\'t be empty. Please select a employee!';
					} else {
						$entity->setEmployee ( $employee );
					}
				}
				
				$validator = new Date ();
				if (! $validator->isValid ( $assignedOn )) {
					$errors [] = 'Assignment date is not correct or empty!';
					$entity->setAssignedOn ( null );
				} else {
					$entity->setAssignedOn ( new \DateTime ( $assignedOn ) );
				}
				
				$entity->setRemarks ( $remarks );
				
				if (count ( $errors ) > 0) {
					
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'errors' => $errors,
							'target' => $entity->getItem(),
							'entity' => $entity 
					) );
				}
				;
				// OK now
				
				$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
						'email' => $this->identity ()
				) );
				
				$entity->setLastChangeBy( $u );
				$entity->setLastChangeOn ( new \DateTime () );
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				
				$this->flashMessenger ()->addMessage ( "Item Assignemt has been updated successfully!" );
				return $this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		// Not Post
		
		// NO POST
		$redirectUrl = Null;
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$entity_id = ( int ) $this->params ()->fromQuery ( 'entity_id' );
		$token = $this->params ()->fromQuery ( 'token' );
		
		$criteria = array (
				'id' => $entity_id,
				'token' => $token 
		);
		
		$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemEmployee' )->findOneBy ( $criteria );
		
		if ($entity !== null) {
			return new ViewModel ( array (
					'redirectUrl' => $redirectUrl,
					'errors' => null,
					'entity' => $entity,
					'target' => $entity->getItem () 
			) );
		} else {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
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
