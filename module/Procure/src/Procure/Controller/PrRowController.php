<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Procure\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use Application\Entity\NmtPmProject;
use Zend\Validator\Date;
use Zend\Math\Rand;
use Application\Entity\NmtProcurePr;
use Application\Entity\NmtProcurePrRow;
use Application\Entity\NmtInventoryItem;

/**
 *
 * @author nmt
 *        
 */
class PrRowController extends AbstractActionController {
	const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
	protected $doctrineEM;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function updateTokenAction() {
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
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->findBy ( $criteria, $sort_criteria );
		
		if (count ( $list ) > 0) {
			foreach ( $list as $entity ) {
				$entity->setChecksum ( md5 ( uniqid ( "pr_row_" . $entity->getId () ) . microtime () ) );
				$entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
			}
		}
		
		$this->doctrineEM->flush ();
		
		/**
		 *
		 * @todo : update index
		 */
		// $this->employeeSearchService->createEmployeeIndex();
		
		$total_records = count ( $list );
		$paginator = null;
		
		if ($total_records > $resultsPerPage) {
			$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtPmProject' )->findBy ( $criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
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
			 * @todo : Update Target
			 */
			$target = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePr' )->findOneBy ( $criteria );
			
			if ($target == null) {
				
				$errors [] = 'Target object can\'t be empty. Or token key is not valid!';
				$this->flashMessenger ()->addMessage ( 'Something wrong!' );
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'errors' => $errors,
						'target' => null,
						'entity' => null 
				) );
				
				// might need redirect
			} else {
				
				$errors = array ();
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$edt = $request->getPost ( 'edt' );
				$isActive = $request->getPost ( 'isActive' );
				$priority = $request->getPost ( 'priority' );
				$quantity = $request->getPost ( 'quantity' );
				
				$remarks = $request->getPost ( 'remarks' );
				$rowCode = $request->getPost ( 'rowCode' );
				$rowName = $request->getPost ( 'rowName' );
				$rowUnit = $request->getPost ( 'rowUnit' );
				$conversionFactor = $request->getPost ( 'conversionFactor' );
				
				$item_id = $request->getPost ( 'item_id' );
				
				if ($isActive != 1) {
					$isActive = 0;
				}
				
				/**
				 *
				 * @todo :
				 */
				$entity = new NmtProcurePrRow ();
				
				if ($item_id > 0) {
					$item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $item_id );
					$entity->setItem ( $item );
				}
				
				$entity->setPr ( $target );
				$entity->setIsActive ( $isActive );
				$entity->setPriority ( $priority );
				$entity->setRemarks ( $remarks );
				$entity->setRowCode ( $rowCode );
				$entity->setRowName ( $rowName );
				$entity->setRowUnit ( $rowUnit );
				$entity->setConversionFactor ( $conversionFactor );
				
				if ($quantity == null) {
					$errors [] = 'Please  enter order quantity!';
				} else {
					
					if (! is_numeric ( $quantity )) {
						$errors [] = 'quantity must be a number.';
					} else {
						if ($quantity <= 0) {
							$errors [] = 'quantity must be greater than 0!';
						}
						$entity->setQuantity ( $quantity );
					}
				}
				
				$validator = new Date ();
				
				// Empty is OK
				if ($edt !== null) {
					if ($edt !== "") {
						
						if (! $validator->isValid ( $edt )) {
							$errors [] = 'Date is not correct or empty!';
						} else {
							$entity->setEdt ( new \DateTime ( $edt ) );
						}
					}
				}
				
				if ($conversionFactor == null) {
					// $errors [] = 'Please enter order quantity!';
				} else {
					
					if (! is_numeric ( $conversionFactor )) {
						$errors [] = 'quantity must be a number.';
					} else {
						if ($conversionFactor <= 0) {
							$errors [] = 'quantity must be greater than 0!';
						}
						$entity->setConversionFactor ( $conversionFactor );
					}
				}
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							
							'redirectUrl' => $redirectUrl,
							'errors' => $errors,
							'entity' => $entity,
							'target' => $target 
					
					) );
				}
				
				// NO ERROR
				$entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
				
				$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
						"email" => $this->identity () 
				) );
				
				$entity->setCreatedBy ( $u );
				$entity->setCreatedOn ( new \DateTime () );
				
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				
				/**
				 *
				 * @todo : UPDATE
				 */
				$entity->setChecksum ( md5 ( uniqid ( "pr_row_" . $entity->getId () ) . microtime () ) );
				$this->doctrineEM->flush ();
				
				$this->flashMessenger ()->addMessage ( "Row '" . $entity->getId () . "' has been created successfully!" );
				return $this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$redirectUrl = null;
		if ($this->getRequest ()->getHeader ( 'Referer' ) !== null) {
			$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		$token = $this->params ()->fromQuery ( 'token' );
		$criteria = array (
				'id' => $id,
				'checksum' => $checksum,
				'token' => $token 
		);
		
		/**
		 *
		 * @todo : Update Target
		 */
		$target = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePr' )->findOneBy ( $criteria );
		
		if ($target !== null) {
			
			return new ViewModel ( array (
					'redirectUrl' => $redirectUrl,
					'errors' => null,
					'target' => $target,
					'entity' => null 
			) );
		} else {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
	}
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function allAction() {
		$item_type = $this->params ()->fromQuery ( 'item_type' );
		$is_active = $this->params ()->fromQuery ( 'is_active' );
		$is_fixed_asset = $this->params ()->fromQuery ( 'is_fixed_asset' );
		
		$sort_by = $this->params ()->fromQuery ( 'sort_by' );
		$sort = $this->params ()->fromQuery ( 'sort' );
		
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
		
		// $n = new NmtInventoryItem();
		if ($sort_by == null) :
			$sort_by = "itemName";
		endif;
		
		if ($sort == null) :
			$sort = "ASC";
		endif;
			
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getAllPrRow ( $sort_by, $sort, 0, 0 );
		
		$total_records = count ( $list );
		$paginator = null;
		
		if ($total_records > $resultsPerPage) {
			$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getAllPrRow ( $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
		}
		
		// $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
		// var_dump (count($all));
		
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => $paginator,
				'sort_by' => $sort_by,
				'sort' => $sort,
				'is_active' => $is_active,
				'is_fixed_asset' => $is_fixed_asset,
				'per_pape' => $resultsPerPage,
				'item_type' => $item_type 
		
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
		$item_type = $this->params ()->fromQuery ( 'item_type' );
		$is_active = $this->params ()->fromQuery ( 'is_active' );
		$is_fixed_asset = $this->params ()->fromQuery ( 'is_fixed_asset' );
		
		$sort_by = $this->params ()->fromQuery ( 'sort_by' );
		$sort = $this->params ()->fromQuery ( 'sort' );
		
		$criteria1 = array ();
		/*
		 * if (! $item_type == null) {
		 * $criteria1 = array (
		 * "itemType" => $item_type
		 * );
		 * }
		 */
		$criteria2 = array ();
		if (! $is_active == null) {
			$criteria2 = array (
					"isActive" => $is_active 
			);
			
			if ($is_active == - 1) {
				$criteria2 = array (
						"isActive" => '0' 
				);
			}
		}
		
		$criteria3 = array ();
		
		if ($sort_by == null) :
			$sort_by = "itemName";
		endif;
		
		if ($sort == null) :
			$sort = "ASC";
		endif;
		
		$sort_criteria = array (
				$sort_by => $sort 
		);
		
		$criteria = array_merge ( $criteria1, $criteria2, $criteria3 );
		
		// var_dump($criteria);
		
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
		
		$query = 'SELECT e, i, pr FROM Application\Entity\NmtProcurePrRow e JOIN e.item i JOIN e.pr pr Where 1=?1';
		
		if (! $is_active == null) {
			if ($is_active == - 1) {
				$query = $query . " AND e.isActive = 0";
			} else {
				$query = $query . " AND e.isActive = 1";
			}
		}
		
		if ($sort_by == "itemName") {
			$query = $query . ' ORDER BY i.' . $sort_by . ' ' . $sort;
		} elseif ($sort_by == "prNumber") {
			$query = $query . ' ORDER BY pr.' . $sort_by . ' ' . $sort;
		}
		$list = $this->doctrineEM->createQuery ( $query )->setParameters ( array (
				"1" => 1 
		) )->getResult ();
		
		$total_records = count ( $list );
		$paginator = null;
		
		if ($total_records > $resultsPerPage) {
			$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
			$list = $this->doctrineEM->createQuery ( $query )->setParameters ( array (
					"1" => 1 
			) )->setFirstResult ( $paginator->minInPage - 1 )->setMaxResults ( ($paginator->maxInPage - $paginator->minInPage) + 1 )->getResult ();
		}
		
		// $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
		// var_dump (count($all));
		
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => $paginator,
				'sort_by' => $sort_by,
				'sort' => $sort,
				'is_active' => $is_active,
				'is_fixed_asset' => $is_fixed_asset,
				'per_pape' => $resultsPerPage,
				'item_type' => $item_type 
		
		) );
	}
	
	/**
	 *
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
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
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		$token = $this->params ()->fromQuery ( 'token' );
		$criteria = array (
				'id' => $target_id,
				'checksum' => $checksum,
				'token' => $token 
		);
		
		/**
		 *
		 * @todo : Change Target
		 */
		$target = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePr' )->findOneBy ( $criteria );
		
		if ($target !== null) {
			
			$criteria = array (
					'pr' => $target_id 
				// 'isActive' => 1,
			);
			
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->findBy ( $criteria );
			$total_records = count ( $list );
			$paginator = null;
			
			return new ViewModel ( array (
					'list' => $list,
					'total_records' => $total_records,
					'paginator' => $paginator,
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
	public function showAction() {
		$request = $this->getRequest ();
		
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		// $u = $this->doctrineEM->getRepository( 'Application\Entity\MlaUsers')->findOneBy(array("email"=>$this->identity() ));
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		$id = ( int ) $this->params ()->fromQuery ( 'entity_id' );
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		$token = $this->params ()->fromQuery ( 'token' );
		$criteria = array (
				'id' => $id,
				'checksum' => $checksum,
				'token' => $token 
		);
		
		$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->findOneBy ( $criteria );
		if ($entity !== null) {
			
			return new ViewModel ( array (
					'redirectUrl' => $redirectUrl,
					'entity' => $entity,
					'errors' => null,
					'target' => $entity->getPR()
			) );
		} else {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
	}
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$errors = array ();
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$entity_id= $request->getPost ( 'entity_id' );
			$token = $request->getPost ( 'token' );
			
			$criteria = array (
					'id' => $entity_id,
					'token' => $token
			);
			
			$entity= $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->findOneBy ( $criteria );
			
			if ($entity== null) {
				
				$errors [] = 'Entity object can\'t be empty. Or token key is not valid!';
				$this->flashMessenger ()->addMessage ( 'Something wrong!' );
				return new ViewModel ( array (
						'redirectUrl' => $redirectUrl,
						'errors' => $errors,
						'target' => null,
						'entity' => null
				) );
				
				// might need redirect
			} else {
				
				$errors = array ();
				$redirectUrl = $request->getPost ( 'redirectUrl' );
				
				$edt = $request->getPost ( 'edt' );
				$isActive = $request->getPost ( 'isActive' );
				$priority = $request->getPost ( 'priority' );
				$quantity = $request->getPost ( 'quantity' );
				
				$remarks = $request->getPost ( 'remarks' );
				$rowCode = $request->getPost ( 'rowCode' );
				$rowName = $request->getPost ( 'rowName' );
				$rowUnit = $request->getPost ( 'rowUnit' );
				$conversionFactor= $request->getPost ( 'conversionFactor' );
				
				$item_id = $request->getPost ( 'item_id' );
				
				if ($isActive != 1) {
					$isActive = 0;
				}
				
				//$entity = new NmtProcurePrRow ();
				
				if ($item_id > 0) {
					$item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $item_id );
					$entity->setItem ( $item );
				}
				
				//$entity->setPr ( $target );
				$entity->setIsActive ( $isActive );
				$entity->setPriority ( $priority );
				$entity->setRemarks ( $remarks );
				$entity->setRowCode ( $rowCode );
				$entity->setRowName ( $rowName );
				$entity->setRowUnit ( $rowUnit );
				$entity->setConversionFactor($conversionFactor);
				
				if ($quantity == null) {
					$errors [] = 'Please  enter order quantity!';
				} else {
					
					if (! is_numeric ( $quantity )) {
						$errors [] = 'quantity must be a number.';
					} else {
						if ($quantity <= 0) {
							$errors [] = 'quantity must be greater than 0!';
						}
						$entity->setQuantity ( $quantity );
					}
				}
				
				$validator = new Date ();
				
				// Empty is OK
				if ($edt !== null) {
					if ($edt !== "") {
						
						if (! $validator->isValid ( $edt )) {
							$errors [] = 'Date is not correct or empty!';
						} else {
							$entity->setEdt ( new \DateTime ( $edt ) );
						}
					}
				}
				
				if ($conversionFactor == null) {
					//$errors [] = 'Please  enter order quantity!';
				} else {
					
					if (! is_numeric ( $conversionFactor)) {
						$errors [] = 'quantity must be a number.';
					} else {
						if ($conversionFactor<= 0) {
							$errors [] = 'quantity must be greater than 0!';
						}
						$entity->setConversionFactor( $conversionFactor);
					}
				}
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							
							'redirectUrl' => $redirectUrl,
							'errors' => $errors,
							'entity' => $entity,
							'target' => $entity->getPr(),
							
					) );
				}
				
				// NO ERROR
				//$entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
				
				$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
						"email" => $this->identity ()
				) );
				
				$entity->setLastChangeBy ( $u );
				$entity->setLastChangeOn( new \DateTime () );
				
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				
				$this->flashMessenger ()->addMessage ( "Row '" . $entity->getId() . "' has been updated successfully!" );
				return $this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		// NO Post
		$redirectUrl = null;
		if ($this->getRequest ()->getHeader ( 'Referer' ) !== null) {
			$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'entity_id' );
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		$token = $this->params ()->fromQuery ( 'token' );
		$criteria = array (
				'id' => $id,
				'checksum' => $checksum,
				'token' => $token
		);
		
		$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->findOneBy ( $criteria );
		
		if ($entity!== null) {
			
			return new ViewModel ( array (
					'redirectUrl' => $redirectUrl,
					'errors' => null,
					'target' => $entity->getPr(),
					'entity' => $entity,
			) );
		} else {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
	}
	
	/**
	 *
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	
	/**
	 *
	 * @param EntityManager $doctrineEM        	
	 * @return \PM\Controller\IndexController
	 */
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
}
