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
use Application\Entity\NmtInventoryItem;
use User\Model\UserTable;
use MLA\Paginator;
use Inventory\Service\ItemSearchService;
use Application\Entity\NmtInventoryItemAttachment;
use Zend\Validator\Date;
use Application\Entity\NmtInventoryItemPurchasing;
use Zend\Math\Rand;

/**
 *
 * @author nmt
 *        
 */
class ItemPurchaseController extends AbstractActionController {
	const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
	protected $doctrineEM;
	protected $itemSearchService;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
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
			 * @todo Update Target
			 */
			$target = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findOneBy ( $criteria );
			
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
				
				$vendor_id = $request->getPost ( 'vendor_id' );
				$currency_id = $request->getPost ( 'currency_id' );
				$pmt_method_id = $request->getPost ( 'pmt_method_id' );
				
				$isActive = ( int ) $request->getPost ( 'isActive' );
				$isPreferredVendor = ( int ) $request->getPost ( 'isPreferredVendor' );
				$leadTime = $request->getPost ( 'leadTime' );
				
				$conversionFactor = $request->getPost ( 'conversionFactor' );
				$priceValidFrom = $request->getPost ( 'priceValidFrom' );
				$priceValidTo = $request->getPost ( 'priceValidTo' );
				$remarks = $request->getPost ( 'remarks' );
				$vendorItemCode = $request->getPost ( 'vendorItemCode' );
				$vendorItemUnit = $request->getPost ( 'vendorItemUnit' );
				$vendorUnitPrice = $request->getPost ( 'vendorUnitPrice' );
				
				if ($isActive !== 1) {
					$isActive = 0;
				}
				
				if ($isPreferredVendor !== 1) {
					$isPreferredVendor = 0;
				}
				
				$entity = new NmtInventoryItemPurchasing ();
				
				$entity->setIsActive ( $isActive );
				$entity->setIsPreferredVendor ( $isPreferredVendor );
				
				$vendor = $this->doctrineEM->find ( 'Application\Entity\NmtBpVendor', $vendor_id );
				
				if ($vendor == null) {
					$errors [] = 'Vendor can\'t be empty. Please select a vendor!';
				} else {
					$entity->setVendor ( $vendor );
				}
				
				if ($vendorItemUnit == null) {
					$errors [] = 'Please enter unit of purchase';
				} else {
					$entity->setVendorItemUnit ( $vendorItemUnit );
					$entity->setConversionText ( $entity->getVendorItemUnit () . ' = ' . $entity->getConversionFactor () . '*' . $target->getStandardUom ()->getUomCode () );
				}
				
				if ($conversionFactor == null) {
					$errors [] = 'Please  enter conversion factor';
				} else {
					
					if (! is_numeric ( $conversionFactor )) {
						$errors [] = 'converstion_factor must be a number.';
					} else {
						if ($conversionFactor <= 0) {
							$errors [] = 'converstion_factor must be greater than 0!';
						}
						$entity->setConversionFactor ( $conversionFactor );
					}
				}
				
				if (! is_numeric ( $vendorUnitPrice )) {
					$errors [] = 'Price is not valid. It must be a number.';
				} else {
					if ($vendorUnitPrice <= 0) {
						$errors [] = 'Price must be greate than 0!';
					}
					$entity->setVendorUnitPrice ( $vendorUnitPrice );
				}
				
				$currency = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCurrency', $currency_id );
				if ($currency == null) {
					$errors [] = 'Curency can\'t be empty. Please select a currency!';
				} else {
					$entity->setCurrency ( $currency );
				}
				
				$pmt_method = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationPmtMethod', $pmt_method_id );
				if (! $pmt_method == null) {
					$entity->setPmtMethod ( $pmt_method );
				}
				
				$date_validated = 0;
				$validator = new Date ();
				if (! $validator->isValid ( $priceValidFrom )) {
					$errors [] = 'Start date is not correct or empty!';
				} else {
					$entity->setPriceValidFrom ( new \DateTime ( $priceValidFrom ) );
					$date_validated ++;
				}
				
				if ($priceValidTo !== "") {
					if (! $validator->isValid ( $priceValidTo )) {
						$errors [] = 'End Date  to is not correct or empty!';
					} else {
						$entity->setPriceValidTo ( new \DateTime ( $priceValidTo ) );
						$date_validated ++;
					}
					
					if ($date_validated == 2) {
						
						if ($priceValidFrom > $priceValidTo) {
							$errors [] = 'End date must be in the future!';
						}
					}
				}
				
				$entity->setItem ( $target );
				$entity->setLeadTime ( $leadTime );
				// $entity->setPmtTerm();
				$entity->setRemarks ( $remarks );
				$entity->setVendorItemCode ( $vendorItemCode );
				
				if (count ( $errors ) > 0) {
					
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'errors' => $errors,
							'target' => $target,
							'entity' => $entity 
					) );
				}
				;
				
				$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
						'email' => $this->identity () 
				) );
				
				$entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
				
				$entity->setCreatedBy ( $u );
				$entity->setCreatedOn ( new \DateTime () );
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				$new_entity_id = $entity->getId ();
				
				$entity->setChecksum ( md5 ( $new_entity_id . uniqid ( microtime () ) ) );
				$this->doctrineEM->flush ();
				
				$this->flashMessenger ()->addMessage ( "Purchasing data has been created successfully!" );
				return $this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
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
	public function showAction() {
		$request = $this->getRequest ();
		
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$entity_id = ( int ) $this->params ()->fromQuery ( 'entity_id' );
		$token = $this->params ()->fromQuery ( 'token' );
		$checksum= $this->params ()->fromQuery ( 'checksum' );
		
		$criteria = array (
				'id' => $entity_id,
				'token' => $token,
				'checksum' => $checksum
		);
		
		$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPurchasing' )->findOneBy ( $criteria );
		if ($entity == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$target = $entity->getItem ();
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
		
		if ($request->isPost ()) {
			$errors = array ();
			
			$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
					"email" => $this->identity () 
			) );
			
			$errors = array ();
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$entity_id = ( int ) $request->getPost ( 'entity_id' );
			$token = $request->getPost ( 'token' );
			
			$criteria = array (
					'id' => $entity_id,
					'token' => $token 
			);
			
			$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPurchasing' )->findOneBy ( $criteria );
			
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
				
				$target = $entity->getItem ();
				
				$vendor_id = $request->getPost ( 'vendor_id' );
				$currency_id = $request->getPost ( 'currency_id' );
				$pmt_method_id = $request->getPost ( 'pmt_method_id' );
				
				$isActive = ( int ) $request->getPost ( 'isActive' );
				$isPreferredVendor = ( int ) $request->getPost ( 'isPreferredVendor' );
				$leadTime = $request->getPost ( 'leadTime' );
				
				$conversionFactor = $request->getPost ( 'conversionFactor' );
				$priceValidFrom = $request->getPost ( 'priceValidFrom' );
				$priceValidTo = $request->getPost ( 'priceValidTo' );
				$remarks = $request->getPost ( 'remarks' );
				$vendorItemCode = $request->getPost ( 'vendorItemCode' );
				$vendorItemUnit = $request->getPost ( 'vendorItemUnit' );
				$vendorUnitPrice = $request->getPost ( 'vendorUnitPrice' );
				
				if ($isActive !== 1) {
					$isActive = 0;
				}
				
				if ($isPreferredVendor !== 1) {
					$isPreferredVendor = 0;
				}
				
				// $entity = new NmtInventoryItemPurchasing ();
				
				$entity->setIsActive ( $isActive );
				$entity->setIsPreferredVendor ( $isPreferredVendor );
				
				$vendor = $this->doctrineEM->find ( 'Application\Entity\NmtBpVendor', $vendor_id );
				
				if ($vendor == null) {
					$errors [] = 'Vendor can\'t be empty. Please select a vendor!';
				} else {
					$entity->setVendor ( $vendor );
				}
				
				if ($vendorItemUnit == null) {
					$errors [] = 'Please enter unit of purchase';
				} else {
					$entity->setVendorItemUnit ( $vendorItemUnit );
					
					if ($target !== null) {
						$entity->setConversionText ( $entity->getVendorItemUnit () . ' = ' . $entity->getConversionFactor () . '*' . $target->getStandardUom ()->getUomCode () );
					}
				}
				
				if ($conversionFactor == null) {
					$errors [] = 'Please  enter conversion factor';
				} else {
					
					if (! is_numeric ( $conversionFactor )) {
						$errors [] = 'converstion_factor must be a number.';
					} else {
						if ($conversionFactor <= 0) {
							$errors [] = 'conversion_factor must be greater than 0!';
						}
						$entity->setConversionFactor ( $conversionFactor );
					}
				}
				
				if (! is_numeric ( $vendorUnitPrice )) {
					$errors [] = 'Price is not valid. It must be a number.';
				} else {
					if ($vendorUnitPrice <= 0) {
						$errors [] = 'Price must be greate than 0!';
					}
					$entity->setVendorUnitPrice ( $vendorUnitPrice );
				}
				
				$currency = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationCurrency', $currency_id );
				if ($currency == null) {
					$errors [] = 'Curency can\'t be empty. Please select a currency!';
				} else {
					$entity->setCurrency ( $currency );
				}
				
				$pmt_method = $this->doctrineEM->find ( 'Application\Entity\NmtApplicationPmtMethod', $pmt_method_id );
				if (! $pmt_method == null) {
					$entity->setPmtMethod ( $pmt_method );
				}
				
				$date_validated = 0;
				$validator = new Date ();
				if (! $validator->isValid ( $priceValidFrom )) {
					$errors [] = 'Start date is not correct or empty!';
				} else {
					$entity->setPriceValidFrom ( new \DateTime ( $priceValidFrom ) );
					$date_validated ++;
				}
				
				if ($priceValidTo !== "") {
					if (! $validator->isValid ( $priceValidTo )) {
						$errors [] = 'End Date  to is not correct or empty!';
					} else {
						$entity->setPriceValidTo ( new \DateTime ( $priceValidTo ) );
						$date_validated ++;
					}
					
					if ($date_validated == 2) {
						
						if ($priceValidFrom > $priceValidTo) {
							$errors [] = 'End date must be in the future!';
						}
					}
				}
				
				// $entity->setItem ( $target );
				
				$entity->setLeadTime ( $leadTime );
				// $entity->setPmtTerm();
				$entity->setRemarks ( $remarks );
				$entity->setVendorItemCode ( $vendorItemCode );
				
				if (count ( $errors ) > 0) {
					
					return new ViewModel ( array (
							'redirectUrl' => $redirectUrl,
							'errors' => $errors,
							'target' => $target,
							'entity' => $entity 
					) );
				}
				;
				
				$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
						'email' => $this->identity () 
				) );
				
				// $entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
				
				$entity->setLastChangeBy ( $u );
				$entity->setLastChangeOn ( new \DateTime () );
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				
				$this->flashMessenger ()->addMessage ( "Purchasing '" . $entity->getId () . "' data has been updated successfully!" );
				return $this->redirect ()->toUrl ( $redirectUrl );
			}
			
			return $this->redirect ()->toUrl ( $redirectUrl );
		}
		
		$redirectUrl = null;
		if ($request->getHeader ( 'Referer' ) == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		$entity_id = ( int ) $this->params ()->fromQuery ( 'entity_id' );
		$token = $this->params ()->fromQuery ( 'token' );
		$checksum= $this->params ()->fromQuery ( 'checksum' );
		
		$criteria = array (
				'id' => $entity_id,
				'token' => $token,
				'checksum' => $checksum
		);
		
		$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPurchasing' )->findOneBy ( $criteria );
		if ($entity == null) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$target = $entity->getItem ();
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
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPurchasing' )->findBy ( $criteria, $sort_criteria );
		$total_records = count ( $list );
		$paginator = null;
		
		if ($total_records > $resultsPerPage) {
			$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPurchasing' )->findBy ( $criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
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
		/*
		 * if (! $request->isXmlHttpRequest ()) {
		 * return $this->redirect ()->toRoute ( 'access_denied' );
		 * }
		 */
		
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
				'item' => $target 
		);
		
		$sort_criteria = array (
				'priceValidFrom' => "DESC" 
		);
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPurchasing' )->findBy ( $criteria, $sort_criteria );
		$total_records = count ( $list );
		$paginator = null;
		
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
				'paginator' => $paginator,
				'target' => $target 
		) );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function updateTokenAction() {
		
		/** @todo: update target */
		$query = 'SELECT e FROM Application\Entity\NmtInventoryItemPurchasing e';
		
		$list = $this->doctrineEM->createQuery($query)
		->getResult();
		
		if (count ( $list ) > 0) {
			foreach ( $list as $entity ) {
				$entity->setChecksum ( md5 ( $entity->getId(). uniqid ( microtime () ) ) );
				$entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
			}
		}
		
		$this->doctrineEM->flush ();
		
		$total_records = count ( $list );
		return new ViewModel ( array (
				'list' => $list,
				'total_records' => $total_records,
		) );
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
	public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable($userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getItemSearchService() {
		return $this->itemSearchService;
	}
	public function setItemSearchService(ItemSearchService $itemSearchService) {
		$this->itemSearchService = $itemSearchService;
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
