<?php

namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Validator\Date;
use Inventory\Model\SparepartCategory;
use Inventory\Model\SparepartCategoryTable;
use Inventory\Model\SparepartCategoryMember;
use Inventory\Model\SparepartCategoryMemberTable;
use Inventory\Model\MLASparepart;
use Inventory\Model\MLASparepartTable;
use MLA\Paginator;
use Inventory\Model\ArticleCategoryMember;
use Inventory\Model\ArticleCategoryMemberTable;
use Inventory\Model\ArticleCategoryTable;
use Inventory\Model\WarehouseTable;
use Inventory\Model\Warehouse;
use Inventory\Model\ArticleMovement;
use Inventory\Model\ArticleMovementTable;
use Application\Model\DepartmentTable;
use User\Model\UserTable;
use Inventory\Model\ArticlePictureTable;
use Inventory\Model\ArticleTable;

class TransactionController extends AbstractActionController {
	protected $sparePartCategoryTable;
	protected $sparePartCategoryMemberTable;
	protected $sparepartTable;
	protected $articleCategoryTable;
	protected $articleCategoryMemberTable;
	protected $articleTable;
	protected $articleMovementTable;
	protected $articlePictureTable;
	
	protected $whTable;
	protected $authService;
	protected $userTable;
	
	protected $movement_type=array(
			'01' => 'STOCK_TRANSFER',
	);
	
	protected $movement_type_issue=array(
			'01' => 'FOR_REPLACEMENT',
			'02' => 'FOR_IE_PROJECT',
			'03' => 'FOR_REPAIR',
			'04' => 'FOR_INSTALLMENT',
	);
	
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function transferAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		
		
		if ($request->isPost ()) {
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );			
			$errors = array ();
			
			$article_id = $request->getPost ( 'article_id' );
			$movement_date = $request->getPost ( 'movement_date' );
			$quantity = $request->getPost ( 'quantity' );
			$source_wh_id = $request->getPost ( 'source_wh_id' );
			$target_wh_id = $request->getPost ( 'target_wh_id' );
			
			$currect_wh_balance=$this->articleMovementTable->getBalanceOfWH($source_wh_id,$article_id);
			
			if ($source_wh_id==$target_wh_id) {
				$errors [] = 'Target Warehouse must be different with Source Warehouse!';
			}
			
			// validator.
			$validator = new Date ();
			
			if (! $validator->isValid ( $movement_date )) {
				$errors [] = 'Transaction date format is not correct!';
			}
			
			if (! is_numeric ( $quantity )) {
				$errors [] = 'Quantity is not valid. It must be a number.';
			} else {
				if ($quantity <= 0) {
					$errors [] = 'Quantity must be greater than 0!';
				}else{
					if($currect_wh_balance<$quantity){
						$errors [] = $quantity . ' is transferred' . ' But there are only ' . $currect_wh_balance . ' in stock';
					}
					
				}
			}
			
			$article = $this->articleTable->get ( $article_id );
			
			if (count ( $errors ) > 0) {
				return new ViewModel ( array (
						'article' => $article,
						'errors' => $errors,
						'redirectUrl' => $redirectUrl 
				) );
			}
			
			// OUT Source WH
			$input = new ArticleMovement ();
			$input->article_id = $article_id;
			$input->movement_date = $movement_date;
			$input->quantity = $quantity;
			$input->flow = 'OUT';
			$input->reason = $request->getPost ( 'reason' );
			$input->requester = $request->getPost ( 'requester' );
			$input->comment = $request->getPost ( 'comment' );
			
			if($source_wh_id==null){
				$input->wh_id = NULL;
			}else{
				$input->wh_id =$source_wh_id;
			}
			$input->movement_type=$this->movement_type['01'];
				
			$input->created_by = $user ['id'];
			
			$this->articleMovementTable->add($input);
			
			// IN Destination WH
			$input = new ArticleMovement ();
			$input->article_id = $article_id;
			$input->movement_date = $movement_date;
			$input->quantity = $quantity;
			$input->flow = 'IN';
			$input->reason = $request->getPost ( 'reason' );
			$input->requester = $request->getPost ( 'requester' );
			$input->comment = $request->getPost ( 'comment' );
			$input->wh_id = $target_wh_id;
			$input->movement_type=$this->movement_type['01'];
			
			$input->created_by = $user ['id'];
			
			
			
			$this->articleMovementTable->add($input);
			
			
			$this->redirect ()->toUrl ( $redirectUrl );
		}
		
		$article_id = ( int ) $this->params ()->fromQuery ( 'article_id' );
		$article = $this->articleTable->get ( $article_id );
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		//echo $this->movement_code['01'];
		return new ViewModel ( array (
				'article' => $article,
				'redirectUrl' => $redirectUrl,
				'errors' => null 
		) );
	}
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function giAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		if ($request->isPost ()) {
			
			$quantity = $request->getPost ( 'quantity' );
			
			
			$input = new ArticleMovement ();
			$input->article_id = $request->getPost ( 'article_id' );
			$input->movement_type = $request->getPost ('movement_type');
				
			$input->movement_date = $request->getPost ( 'movement_date');
			$input->quantity = $request->getPost ( 'quantity' );
			$input->flow = 'OUT';
			$input->reason = $request->getPost ( 'reason' );
			$input->requester = $request->getPost ( 'requester' );
			$input->comment = $request->getPost ( 'comment' );
			$input->created_on = $request->getPost ( 'created_on' );
				
			$instock = $request->getPost ( 'instock' );
			$redirectUrl = $request->getPost ( 'redirectUrl' );
				
			// validator.
			$validator = new Date ();
				
			$errors = array ();
				
			if (! $validator->isValid ( $input->movement_date )) {
				$errors [] = 'Transaction date format is not correct!';
			}
						
			if (! is_numeric ( $quantity )) {
				$errors [] = 'Quantity is not valid. It must be a number.';
			} else {
				if ($quantity <= 0) {
					$errors [] = 'Quantity must be greater than 0!';
				}else{
					if($quantity > $instock){
						$errors [] = 'There are only ' . $instock . ' in stock';
					}
						
				}
			}
			if (count ( $errors ) > 0) {
		
				$id = ( int ) $request->getPost ( 'article_id' );
				$article = $this->articleTable->getArticleByID ( $id );
				$instock=$this->articleMovementTable->getBalanceOfWH(NUll,$id);
				
				$pictures = $this->articlePictureTable->getArticlePicturesById ( $id );
		
				return new ViewModel ( array (
						'article' => $article,
						'pictures' => $pictures,
						'instock' => $instock,
						'errors' => $errors,
						'redirectUrl' => $redirectUrl,
						'movement' => $input
				) );
			} else {
		
				// Validated
				$this->articleMovementTable->add ( $input );
				$this->redirect ()->toUrl ( $redirectUrl );
			}
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'article_id' );
		$article = $this->articleTable->getArticleByID ( $id );
		$pictures = $this->articlePictureTable->getArticlePicturesById ( $id );
		
		$instock=$this->articleMovementTable->getBalanceOfWH(NUll,$id);
		
		return new ViewModel ( array (
				'article' => $article,
				'pictures' => $pictures,
				'instock' => $instock,
				'errors' => null,
				'redirectUrl' => $redirectUrl,
				'movement_types'=>$this->movement_type_issue,
				'movement' => null
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function grAction() {
		$categories = $this->sparePartCategoryTable->fetchAll ();
		return new ViewModel ( array (
				'categories' => $categories 
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function balanceAction() {
		$request = $this->getRequest ();
		
		$article_id = ( int ) $this->params ()->fromQuery ( 'article_id' );
		$article = $this->articleTable->get ( $article_id );
		$balances=$this->articleMovementTable->getBalanceByArticle($article_id);
		if ($request->isXmlHttpRequest ()) {
			$this->layout ( "layout/inventory/ajax" );
		}
		return new ViewModel ( array (
				'article' => $article,
				'balances' => $balances,
		) 
		);
	}
	
	// Setter and getter
	public function getSparePartCategoryTable() {
		return $this->sparePartCategoryTable;
	}
	public function setSparePartCategoryTable(SparepartCategoryTable $sparePartCategoryTable) {
		$this->sparePartCategoryTable = $sparePartCategoryTable;
		return $this;
	}
	public function getSparePartCategoryMemberTable() {
		return $this->sparePartCategoryMemberTable;
	}
	public function setSparePartCategoryMemberTable(SparepartCategoryMemberTable $sparePartCategoryMemberTable) {
		$this->sparePartCategoryMemberTable = $sparePartCategoryMemberTable;
		return $this;
	}
	public function getSparepartTable() {
		return $this->sparepartTable;
	}
	public function setSparepartTable(MLASparepartTable $sparepartTable) {
		$this->sparepartTable = $sparepartTable;
		return $this;
	}
	public function getArticleCategoryTable() {
		return $this->articleCategoryTable;
	}
	public function setArticleCategoryTable(ArticleCategoryTable $articleCategoryTable) {
		$this->articleCategoryTable = $articleCategoryTable;
		return $this;
	}
	public function getArticleCategoryMemberTable() {
		return $this->articleCategoryMemberTable;
	}
	public function setArticleCategoryMemberTable(ArticleCategoryMemberTable $articleCategoryMemberTable) {
		$this->articleCategoryMemberTable = $articleCategoryMemberTable;
		return $this;
	}
	public function getArticleTable() {
		return $this->articleTable;
	}
	public function setArticleTable(ArticleTable $articleTable) {
		$this->articleTable = $articleTable;
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
	public function setUserTable(UserTable $userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getWhTable() {
		return $this->whTable;
	}
	public function setWhTable(WarehouseTable $whTable) {
		$this->whTable = $whTable;
		return $this;
	}
	public function getArticleMovementTable() {
		return $this->articleMovementTable;
	}
	public function setArticleMovementTable(ArticleMovementTable $articleMovementTable) {
		$this->articleMovementTable = $articleMovementTable;
		return $this;
	}
	public function getArticlePictureTable() {
		return $this->articlePictureTable;
	}
	public function setArticlePictureTable(ArticlePictureTable $articlePictureTable) {
		$this->articlePictureTable = $articlePictureTable;
		return $this;
	}
	
}
