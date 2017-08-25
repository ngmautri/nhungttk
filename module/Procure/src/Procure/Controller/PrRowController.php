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
use Zend\Escaper\Escaper;
use Procure\Service\PrSearchService;
/**
 *
 * @author nmt
 *        
 */
class PrRowController extends AbstractActionController {
	const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
	protected $doctrineEM;
	protected $prSearchService;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
     * @return the $prSearchService
     */
    public function getPrSearchService()
    {
        return $this->prSearchService;
    }

    /**
     * @param field_type $prSearchService
     */
    public function setPrSearchService(PrSearchService $prSearchService)
    {
        $this->prSearchService = $prSearchService;
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
				$project_id = $request->getPost ( 'project_id' );
				
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
				
				if ($project_id > 0) {
					$project = $this->doctrineEM->find ( 'Application\Entity\NmtPmProject', $project_id );
					if ($project !== null) {
						$entity->setProject ( $project );
					}
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
				
				$index_update_status = $this->prSearchService->updateIndex(1,$entity, fasle);
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
		
	//	$this->layout ( "layout/fluid" );
		
		
		$item_type = $this->params ()->fromQuery ( 'item_type' );
		$is_active = (int) $this->params ()->fromQuery ( 'is_active' );
		$is_fixed_asset = (int) $this->params ()->fromQuery ( 'is_fixed_asset' );
		
		$sort_by = $this->params ()->fromQuery ( 'sort_by' );
		$sort = $this->params ()->fromQuery ( 'sort' );
		$balance = $this->params ()->fromQuery ( 'balance' );
		$pr_year = $this->params ()->fromQuery ( 'pr_year' );
		
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
		
		if ($sort_by == null) :
			//$sort_by = "prNumber";
			$sort_by = "itemName";
		endif;
		
			// $n = new NmtInventoryItem();
		if ($balance == null) :
			$balance = 1;
		endif;
		
		if ($is_active == null) :
		  $is_active = 1;
		endif;
		
		
			// $n = new NmtInventoryItem();
		if ($pr_year == null) :
			$pr_year = date ( 'Y' );
		endif;
		
		if ($sort == null) :
			$sort = "ASC";
		endif;
		
			// $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
			// var_dump (count($all));
		
		return new ViewModel ( array (
				'sort_by' => $sort_by,
				'sort' => $sort,
				'is_active' => $is_active,
				'is_fixed_asset' => $is_fixed_asset,
				'per_pape' => $resultsPerPage,
				'item_type' => $item_type,
				'balance' => $balance,
				'pr_year' => $pr_year 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function all1Action() {
		$item_type = $this->params ()->fromQuery ( 'item_type' );
		$is_active = $this->params ()->fromQuery ( 'is_active' );
		$is_fixed_asset = $this->params ()->fromQuery ( 'is_fixed_asset' );
		
		$sort_by = $this->params ()->fromQuery ( 'sort_by' );
		$sort = $this->params ()->fromQuery ( 'sort' );
		$balance = $this->params ()->fromQuery ( 'balance' );
		$pr_year = $this->params ()->fromQuery ( 'pr_year' );
		
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
		
			// $n = new NmtInventoryItem();
		if ($balance == null) :
			$balance = 1;
		endif;
		
			// $n = new NmtInventoryItem();
		if ($pr_year == null) :
			$pr_year = date ( 'Y' );
		endif;
		
		if ($sort == null) :
			$sort = "ASC";
		endif;
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getAllPrRow ( $pr_year, $balance, $sort_by, $sort, 0, 0 );
		
		$total_records = count ( $list );
		$paginator = null;
		
		if ($total_records > $resultsPerPage) {
			$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getAllPrRow ( $pr_year, $balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
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
				'item_type' => $item_type,
				'balance' => $balance,
				'pr_year' => $pr_year 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function girdAllAction() {
		// $sort_by = $this->params ()->fromQuery ( 'sort_by' );
		if (isset ( $_GET ['sort_by'] )) {
			$sort_by = $_GET ['sort_by'];
		} else {
			$sort_by = "itemName";
		}
		// $sort = $this->params ()->fromQuery ( 'sort' );
		
		if (isset ( $_GET ['sort'] )) {
			$sort = $_GET ['sort'];
		} else {
			$sort = "ASC";
		}
		
		// $balance = $this->params ()->fromQuery ( 'balance' );
		
		if (isset ( $_GET ['balance'] )) {
			
			$balance = $_GET ['balance'];
		} else {
			$balance = 1;
		}
		
		if (isset ( $_GET ['is_active'] )) {		    
		    $is_active = (int) $_GET ['is_active'];
		} else {
		    $is_active = 1;
		}
		
		// $pr_year = $this->params ()->fromQuery ( 'pr_year' );
		
		if (isset ( $_GET ['pr_year'] )) {
			
			$pr_year = $_GET ['pr_year'];
		} else {
			$pr_year = date ( 'Y' );
		}
		
		if (isset ( $_GET ["pq_curpage"] )) {
			$pq_curPage = $_GET ["pq_curpage"];
		} else {
			$pq_curPage = 1;
		}
		
		if (isset ( $_GET ["pq_rpp"] )) {
			$pq_rPP = $_GET ["pq_rpp"];
		} else {
			$pq_rPP = 1;
		}
		
		$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getAllPrRow ($is_active, $pr_year, $balance, $sort_by, $sort, 0, 0 );
		
		$total_records = count ( $list );
		$paginator = null;
		
		$a_json_final = array ();
		$a_json = array ();
		$a_json_row = array ();
		$escaper = new Escaper ();
		
		if ($total_records > 0) {
			
			if ($total_records > $pq_rPP) {
				$paginator = new Paginator ( $total_records, $pq_curPage, $pq_rPP );
				$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getAllPrRow ( $is_active, $pr_year, $balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
			}
			$count = 0;
			foreach ( $list as $a ) {
				
				$item_detail = "/inventory/item/show1?token=" . $a ['item_token'] . "&checksum=" . $a ['item_checksum'] . "&entity_id=" . $a ['item_id'];
				if ($a ['item_name'] !== null) {
					$onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs ( $a ['item_name'] ) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
				} else {
					$onclick = "showJqueryDialog('Detail of Item: " . ($a ['item_name']) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
				}
				
				$count ++;
				if ($paginator == null) {
					$a_json_row ["row_number"] = $count;
				} else {
					$a_json_row ["row_number"] = $paginator->minInPage - 1 + $count;
				}
				
				$a_json_row ["pr_number"] = $a ['pr_number'] . '<a style="" target="blank"  title="'. $a ['pr_number'] .'" href="/procure/pr/show?token='.$a ["pr_token"].'&entity_id='. $a ["pr_id"]. '&checksum='.$a ["pr_checksum"].'" >&nbsp;&nbsp;...&nbsp;&nbsp;</span></a>';
	
				if($a ['submitted_on']!== null ){
				    $a_json_row ['pr_submitted_on'] = date_format(date_create($a ['submitted_on']),'d-m-y');
				    //$a_json_row ['pr_submitted_on'] = $a ['submitted_on'];
				}else{
				    $a_json_row ['pr_submitted_on'] = '';
				}
				
				
				$a_json_row ["row_id"] = $a ['id'];
				$a_json_row ["row_token"] = $a ['token'];
				$a_json_row ["row_checksum"] = $a ['checksum'];
				
				
			
				$a_json_row ["item_sku"] = '<span title="' .$a ['item_sku'] . '">'. substr($a ['item_sku'],0,5) . '</span>';
				
	                if (strlen ( $a ['item_name'] ) < 35) {
	                    $a_json_row ["item_name"] = $a ['item_name'] . '<a style="cursor:pointer;color:blue"  item-pic="" id="'.$a['item_id'] .'" item_name="'.$a['item_name'].'" title="'. $a ['item_name'] .'" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;....&nbsp;&nbsp;</a>';
	                }else{
	                     $a_json_row ["item_name"] = substr($a ['item_name'],0,30). '<a style="cursor:pointer;;color:blue"  item-pic="" id="'.$a['item_id'] .'" item_name="'.$a['item_name'].'" title="'. $a ['item_name'] .'" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</a>';
					}
				
				$a_json_row ["quantity"] = $a ['quantity'];
				$a_json_row ["confirmed_balance"] = $a ['confirmed_balance'];
				
				if (strlen ( $a ['vendor_name'] ) < 10) {
					$a_json_row ["vendor_name"] = $a ['vendor_name'];
				} else {
					$a_json_row ["vendor_name"] = '<span title="'. $a ['vendor_name'] .'">'.substr ( $a ['vendor_name'], 0, 8 ) . '...</span>';
				}
				
				
				if ($a ['vendor_unit_price'] !== null) {
					$a_json_row ["vendor_unit_price"] = number_format ( $a ['vendor_unit_price'], 2 );
				} else {
					$a_json_row ["vendor_unit_price"] = 0;
				}
				
				$a_json_row ["currency"] = $a ['currency'];
				
				$received_detail = "/inventory/item-transaction/pr-row?pr_row_id=" . $a ['id'];
				
				if ($a ['item_name'] !== null) {
					$onclick1 = "showJqueryDialog('Receiving of Item: " . $escaper->escapeJs ( $a ['item_name'] ) . "','1200',$(window).height()-100,'" . $received_detail . "','j_loaded_data', true);";
				} else {
					$onclick1 = "showJqueryDialog('Receiving of Item: " . ($a ['item_name']) . "','1200', $(window).height()-100,'" . $received_detail . "','j_loaded_data', true);";
				}
				
				if ($a ['total_received'] > 0) {
					$a_json_row ["total_received"] = '<a style="color: #337ab7;" href="javascript:;" onclick="' . $onclick1 . '" >' . $a ['total_received'] . '</a>';
				} else {
					$a_json_row ["total_received"] = "";
				}
				
				$a_json_row ["project_id"] = $a ['project_id'];
				
				if (strlen ( $a ['remarks'] ) < 20) {
					$a_json_row ["remarks"] = $a ['remarks'];
				}else{
					$a_json_row ["remarks"] = '<span title="'. $a ['remarks'] .'">'.substr ( $a ['remarks'], 0, 15 ) . '...</span>';
					
				}
				$a_json_row ["fa_remarks"] = $a ['fa_remarks'];
				$a_json_row ["receipt_date"] ="";
				$a_json_row ["vendor"] ="";
				$a_json_row ["vendor_id"] ="";
				
				$a_json [] = $a_json_row;
			}
			
			$a_json_final ['data'] = $a_json;
			$a_json_final ['totalRecords'] = $total_records;
			$a_json_final ['curPage'] = $pq_curPage;
		}
		
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
		$response->setContent ( json_encode ( $a_json_final ) );
		return $response;
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
		$balance = $this->params ()->fromQuery ( 'balance' );
		
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
				'item_type' => $item_type,
				'balance' => $balance 
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
	public function rowAction() {
		$request = $this->getRequest ();
		
		$sort_by = $this->params ()->fromQuery ( 'sort_by' );
		$sort = $this->params ()->fromQuery ( 'sort' );
		$balance = $this->params ()->fromQuery ( 'balance' );
		$is_active = $this->params ()->fromQuery ( 'is_active' );
		
		if ($sort_by == null) :
			$sort_by = "createdOn";
		endif;
		
		if ($balance == null) :
			$balance = 2;
		endif;
		
		if ($sort == null) :
			$sort = "ASC";
		endif;
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) ) or $this->params ()->fromQuery ( 'perPage' ) == null) {
			$resultsPerPage = 30;
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
		
		// accepted only ajax request
		if (! $request->isXmlHttpRequest ()) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
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
			
			// $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->get ( $criteria );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getPrRow ( $target_id, $balance, $sort_by, $sort, 0, 0 );
			
			$total_records = count ( $list );
			$paginator = null;
			
			if ($total_records > $resultsPerPage) {
				$paginator = new Paginator ( $total_records, $page, $resultsPerPage );
				$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getPrRow ( $target_id, $balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
			}
			
			return new ViewModel ( array (
					'list' => $list,
					'total_records' => $total_records,
					'paginator' => $paginator,
					'target' => $target,
					'sort_by' => $sort_by,
					'sort' => $sort,
					'per_pape' => $resultsPerPage,
					'balance' => $balance,
					'is_active' => $is_active 
			
			) );
		} else {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
	}
	
	/**
	 *
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function girdAction() {
		$request = $this->getRequest ();
		
		$sort_by = $this->params ()->fromQuery ( 'sort_by' );
		$sort = $this->params ()->fromQuery ( 'sort' );
		$balance = $this->params ()->fromQuery ( 'balance' );
		$is_active = $this->params ()->fromQuery ( 'is_active' );
		
		if ($sort_by == null) :
			$sort_by = "createdOn";
		endif;
		
		if ($balance == null) :
			$balance = 2;
		endif;
		
		if ($sort == null) :
			$sort = "ASC";
		endif;
		
		$pq_curPage = $_GET ["pq_curpage"];
		$pq_rPP = $_GET ["pq_rpp"];
		
		if (is_null ( $this->params ()->fromQuery ( 'perPage' ) ) or $this->params ()->fromQuery ( 'perPage' ) == null) {
			$resultsPerPage = 30;
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
		
		$a_json_final = array ();
		$a_json = array ();
		$a_json_row = array ();
		$escaper = new Escaper ();
		
		if ($target !== null) {
			
			$criteria = array (
					'pr' => $target_id 
				// 'isActive' => 1,
			);
			
			// $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->get ( $criteria );
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getPrRow ( $target_id, $balance, $sort_by, $sort, 0, 0 );
			$total_records = 0;
			if (count ( $list ) > 0) {
				
				$total_records = count ( $list );
				
				if ($total_records > $pq_rPP) {
					$paginator = new Paginator ( $total_records, $pq_curPage, $pq_rPP );
					$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getPrRow ( $target_id, $balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
				}
				
				foreach ( $list as $a ) {
					
				    
				    $item_detail = "/inventory/item/show1?token=" . $a ['item_token'] . "&checksum=" . $a ['item_checksum'] . "&entity_id=" . $a ['item_id'];
				    if ($a ['item_name'] !== null) {
				        $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs ( $a ['item_name'] ) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
				    } else {
				        $onclick = "showJqueryDialog('Detail of Item: " . ($a ['item_name']) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
				    }
					
					$a_json_row ["row_id"] = $a ['id'];
					$a_json_row ["row_token"] = $a ['token'];
					$a_json_row ["row_checksum"] = $a ['checksum'];
					
					$a_json_row ["item_sku"] = '<span title="' .$a ['item_sku'] . '">'. substr($a ['item_sku'],0,5) . '</span>';
					
					if (strlen ( $a ['item_name'] ) < 35) {
					    $a_json_row ["item_name"] = $a ['item_name'] . '<a style="cursor:pointer;color:blue"  item-pic="" id="'.$a['item_id'] .'" item_name="'.$a['item_name'].'" title="'. $a ['item_name'] .'" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;....&nbsp;&nbsp;</a>';
					}else{
					    $a_json_row ["item_name"] = substr($a ['item_name'],0,30). '<a style="cursor:pointer;;color:blue"  item-pic="" id="'.$a['item_id'] .'" item_name="'.$a['item_name'].'" title="'. $a ['item_name'] .'" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</a>';
					}
					
					$a_json_row ["quantity"] = $a ['quantity'];
					$a_json_row ["confirmed_balance"] = $a ['confirmed_balance'];
					
					$received_detail = "/inventory/item-transaction/pr-row?pr_row_id=" . $a ['id'];
					$onclick1 = "showJqueryDialog('Receiving of Item: " . $escaper->escapeJs ( $a ['item_name'] ) . "','1200',$(window).height()-100,'" . $received_detail . "','j_loaded_data', true);";
					
					if ($a ['total_received'] > 0) {
						$a_json_row ["total_received"] = '<a style="color: #337ab7;" href="javascript:;" onclick="' . $onclick1 . '" >' . $a ['total_received'] . '</a>';
					} else {
						$a_json_row ["total_received"] = "";
					}
					
					$a_json_row ["confirmed_balance"] = $a ['confirmed_balance'];
					
					if (strlen ( $a ['vendor_name'] ) < 10) {
						$a_json_row ["vendor_name"] = $a ['vendor_name'];
					} else {
						$a_json_row ["vendor_name"] = '<span title="'. $a ['vendor_name'] .'">'.substr ( $a ['vendor_name'], 0, 8 ) . '...</span>';
					}
					
					if ($a ['vendor_unit_price'] !== null) {
						$a_json_row ["vendor_unit_price"] = number_format ( $a ['vendor_unit_price'], 2 );
					} else {
						$a_json_row ["vendor_unit_price"] = 0;
					}
					
					$a_json_row ["currency"] = $a ['currency'];
					
					$a_json_row ["project_id"] = $a ['project_id'];
					
					if (strlen ( $a ['remarks'] ) < 20) {
						$a_json_row ["remarks"] = $a ['remarks'];
					}else{
						$a_json_row ["remarks"] = '<span title="'. $a ['remarks'] .'">'.substr ( $a ['remarks'], 0, 15 ) . '...</span>';
						
					}
					$a_json_row ["fa_remarks"] = $a ['fa_remarks'];
					
					
					$a_json [] = $a_json_row;
				}
			}
			
			$a_json_final ['data'] = $a_json;
			$a_json_final ['totalRecords'] = $total_records;
			$a_json_final ['curPage'] = $pq_curPage;
		}
		
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
		$response->setContent ( json_encode ( $a_json_final ) );
		return $response;
	}
	
	/**
	 *
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function grGirdAction() {
	    $request = $this->getRequest ();
	    
	    $sort_by = $this->params ()->fromQuery ( 'sort_by' );
	    $sort = $this->params ()->fromQuery ( 'sort' );
	    $balance = $this->params ()->fromQuery ( 'balance' );
	    $is_active = $this->params ()->fromQuery ( 'is_active' );
	    
	    if ($sort_by == null) :
	    $sort_by = "createdOn";
	    endif;
	    
	    if ($balance == null) :
	    $balance = 2;
	    endif;
	    
	    if ($sort == null) :
	    $sort = "ASC";
	    endif;
	    
	    $pq_curPage = $_GET ["pq_curpage"];
	    $pq_rPP = $_GET ["pq_rpp"];
	    
	    if (is_null ( $this->params ()->fromQuery ( 'perPage' ) ) or $this->params ()->fromQuery ( 'perPage' ) == null) {
	        $resultsPerPage = 30;
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
	    
	    $a_json_final = array ();
	    $a_json = array ();
	    $a_json_row = array ();
	    $escaper = new Escaper ();
	    
	    if ($target !== null) {
	        
	        $criteria = array (
	            'pr' => $target_id
	            // 'isActive' => 1,
	        );
	        
	        // $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->get ( $criteria );
	        $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getPrRow ( $target_id, $balance, $sort_by, $sort, 0, 0 );
	        $total_records = 0;
	        if (count ( $list ) > 0) {
	            
	            $total_records = count ( $list );
	            
	            if ($total_records > $pq_rPP) {
	                $paginator = new Paginator ( $total_records, $pq_curPage, $pq_rPP );
	                $list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->getPrRow ( $target_id, $balance, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1 );
	            }
	            
	            foreach ( $list as $a ) {
	                
	                $item_detail = "/inventory/item/show1?token=" . $a ['item_token'] . "&checksum=" . $a ['item_checksum'] . "&entity_id=" . $a ['item_id'];
	                
	                if ($a ['item_name'] !== null) {
	                    $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs ( $a ['item_name'] ) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
	                } else {
	                    $onclick = "showJqueryDialog('Detail of Item: " . ($a ['item_name']) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
	                }
	                
	                
	                $a_json_row ["row_id"] = $a ['id'];
	                $a_json_row ["row_token"] = $a ['token'];
	                $a_json_row ["row_checksum"] = $a ['checksum'];
	                $a_json_row ["item_sku"] = '<span title="SKU: ' .$a ['item_sku'] . '">'. substr($a ['item_sku'],0,5) . '</span>';
	                
			        
	                if (strlen ( $a ['item_name'] ) < 40) {
$a_json_row ["item_name"] = '<a style="cursor:pointer;"  item-pic="" id="'.$a['item_id'] .'" item_name="'.$a['item_name'].'" title="'. $a ['item_name'] .'" href="javascript:;" onclick="' . $onclick . '" >' . $a ['item_name'] .'</a>';
	                }else{
	                    $a_json_row ["item_name"] = '<a style="" title="'. $a ['item_name'] . '" href="javascript:;" onclick="' . $onclick . '" >' . substr($a ['item_name'],0,36).' ...</a>';
	                $a_json_row ["item_name"] = '<a style="cursor:pointer;"  item-pic="" id="'.$a['item_id'] .'" item_name="'.$a['item_name'].'" title="'. $a ['item_name'] .'" href="javascript:;" onclick="' . $onclick . '" >' . substr($a ['item_name'],0,36) .'</a>';
					}
	                
	                $a_json_row ["quantity"] = $a ['quantity'];
	                $a_json_row ["confirmed_balance"] = $a ['confirmed_balance'];
	                
	                $received_detail = "/inventory/item-transaction/pr-row?pr_row_id=" . $a ['id'];
	                $onclick1 = "showJqueryDialog('Receiving of Item: " . $escaper->escapeJs ( $a ['item_name'] ) . "','1200',$(window).height()-100,'" . $received_detail . "','j_loaded_data', true);";
	                
	                if ($a ['total_received'] > 0) {
	                    $a_json_row ["total_received"] = '<a style="color: #337ab7;" href="javascript:;" onclick="' . $onclick1 . '" >' . $a ['total_received'] . '</a>';
	                } else {
	                    $a_json_row ["total_received"] = "";
	                }
	                
	                $a_json_row ["confirmed_balance"] = $a ['confirmed_balance'];
	                
	                $a_json_row ["vendor_name"] = "";
	                $a_json_row ["receipt_quantity"] = "";
	                $a_json_row ["unit"] = "";
	                $a_json_row ["convert_factory"] = "";
	                $a_json_row ["unit_price"] = "";
	                $a_json_row ["currency"] = "";
	                $a_json_row ["remarks"] = "";
	                $a_json [] = $a_json_row;
	            }
	        }
	        
	        $a_json_final ['data'] = $a_json;
	        $a_json_final ['totalRecords'] = $total_records;
	        $a_json_final ['curPage'] = $pq_curPage;
	    }
	    
	    $response = $this->getResponse ();
	    $response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
	    $response->setContent ( json_encode ( $a_json_final ) );
	    return $response;
	}
	
	/**
	 *
	 * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
	 */
	public function updateRowAction() {
	    
	    $a_json_final = array ();
	    $escaper = new Escaper ();
	
	    /* $pq_curPage = $_GET ["pq_curpage"];
	    $pq_rPP = $_GET ["pq_rpp"];
	     */
	    $sent_list = json_decode($_POST['sent_list'], true);
	    //echo json_encode($sent_list);
	    
	    $to_update = $sent_list['updateList'];
	    foreach ($to_update as $a){
	        $criteria = array (
	            'id' => $a['row_id'],
	            'token' => $a['row_token']
	        );
	        
	        /** @var \Application\Entity\NmtProcurePrRow $entity */
	        $entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->findOneBy ( $criteria );
	        
	        if ($entity != null) {
	            $entity->setFaRemarks($a['fa_remarks']);
	            //$a_json_final['updateList']=$a['row_id'] . 'has been updated';
	            $this->doctrineEM->persist ( $entity );
	            
	        }
	    }
	    $this->doctrineEM->flush ();
	    
	    //$a_json_final["updateList"]= json_encode($sent_list["updateList"]);
	    
	    $response = $this->getResponse ();
	    $response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/json' );
	    $response->setContent ( json_encode ( $sent_list ) );
	    return $response;
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
					'target' => $entity->getPR () 
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
			$entity_id = $request->getPost ( 'entity_id' );
			$token = $request->getPost ( 'token' );
			
			$criteria = array (
					'id' => $entity_id,
					'token' => $token 
			);
			
			$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtProcurePrRow' )->findOneBy ( $criteria );
			
			if ($entity == null) {
				
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
				$conversionFactor = $request->getPost ( 'conversionFactor' );
				
				$item_id = $request->getPost ( 'item_id' );
				$project_id = $request->getPost ( 'project_id' );
				
				if ($isActive != 1) {
					$isActive = 0;
				}
				
				// $entity = new NmtProcurePrRow ();
				
				if ($item_id > 0) {
					$item = $this->doctrineEM->find ( 'Application\Entity\NmtInventoryItem', $item_id );
					$entity->setItem ( $item );
				}
				
				if ($project_id > 0) {
					$project = $this->doctrineEM->find ( 'Application\Entity\NmtPmProject', $project_id );
					if ($project !== null) {
						$entity->setProject ( $project );
					}
				}
				
				// $entity->setPr ( $target );
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
						} else {
							$entity->setQuantity ( $quantity );
						}
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
						$errors [] = 'Conversion Factor must be a number.';
					} else {
						if ($conversionFactor <= 0) {
							$errors [] = 'Conversion Factor must be greater than 0!';
						} else {
							$entity->setConversionFactor ( $conversionFactor );
						}
					}
				}
				
				if (count ( $errors ) > 0) {
					return new ViewModel ( array (
							
							'redirectUrl' => $redirectUrl,
							'errors' => $errors,
							'entity' => $entity,
							'target' => $entity->getPr () 
					
					) );
				}
				
				// NO ERROR
				// $entity->setToken ( Rand::getString ( 10, self::CHAR_LIST, true ) . "_" . Rand::getString ( 21, self::CHAR_LIST, true ) );
				
				$u = $this->doctrineEM->getRepository ( 'Application\Entity\MlaUsers' )->findOneBy ( array (
						"email" => $this->identity () 
				) );
				
				$entity->setLastChangeBy ( $u );
				$entity->setLastChangeOn ( new \DateTime () );
				
				$this->doctrineEM->persist ( $entity );
				$this->doctrineEM->flush ();
				
				$index_update_status = $this->prSearchService->updateIndex(0, $entity, fasle);
				
				$this->flashMessenger ()->addMessage ( $index_update_status );
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
		
		if ($entity !== null) {
			
			return new ViewModel ( array (
					'redirectUrl' => $redirectUrl,
					'errors' => null,
					'target' => $entity->getPr (),
					'entity' => $entity 
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
