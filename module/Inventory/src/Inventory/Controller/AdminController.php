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
use Zend\View\Model\ViewModel;
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

use Application\Model\DepartmentTable;
use User\Model\UserTable;
class AdminController extends AbstractActionController {
	protected $sparePartCategoryTable;
	protected $sparePartCategoryMemberTable;
	protected $sparepartTable;
	
	protected $articleCategoryTable;
	protected $articleCategoryMemberTable;
	protected $articleTable;
	
	protected $authService;
	protected $userTable;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function sparepartCategoryAction() {
		$categories = $this->sparePartCategoryTable->fetchAll ();
		return new ViewModel ( array (
				'categories' => $categories 
		) );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editSparepartCategoryAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
		
		
		if ($request->isPost ()) {
			
			$category = new SparepartCategory ();
			$category->name = $request->getPost ( 'name' );
			$category->description = $request->getPost ( 'description' );
			$category->parent_id = $request->getPost ( 'parent_id' );
			
			$this->sparePartCategoryTable->update ( $category, $request->getPost ( 'id' ) );
			
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
			$this->redirect()->toUrl($redirectUrl);					
	
		} else {
			$id = ( int ) $this->params ()->fromQuery ( 'id' );
			$category = $this->sparePartCategoryTable->get ( $id );
			
			return new ViewModel ( array (
					'category' => $category,
					'redirectUrl'=>$redirectUrl,
						
			) );
		}
	}
	
	public function addSparepartCategoryAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
		if ($request->isPost ()) {
			
			$category = new SparepartCategory ();
			$category->name = $request->getPost ( 'name' );
			$category->description = $request->getPost ( 'description' );
			$category->parent_id = $request->getPost ( 'parent_id' );
			$this->sparePartCategoryTable->add ( $category );
			
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
			$this->redirect()->toUrl($redirectUrl);					
		}
		
		
		return new ViewModel ( array (
				'redirectUrl'=>$redirectUrl,
			)
		);
		
		
	}
	
	public function addSparepartCategoryMemberAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
		
		if ($request->isPost ()) {
			
			$category_id = ( int ) $request->getPost ( 'id' );
			$spareparts = $request->getPost ( 'sparepart' );
			
			if (count ( $spareparts ) > 0) {
				
				foreach ( $spareparts as $sp ) {
					$member = new SparepartCategoryMember ();
					$member->sparepart_cat_id = $category_id;
					$member->sparepart_id = $sp;
					
					if ($this->sparePartCategoryMemberTable->isMember ( $sp, $category_id ) == false) {
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
		$category = $this->sparePartCategoryTable->get ( $id );
		
		$spareparts = $this->sparePartCategoryMemberTable->getNoneMembersOfCatId($id);
		
		return new ViewModel ( array (
				'category' => $category,
				'spareparts' => $spareparts,
				'redirectUrl'=>$redirectUrl,
			)
		);
	}
	
	/*
	 * 
	 */
	public function addArticleCategoryMemberAction() {
		$request = $this->getRequest ();
		$identity = $this->authService->getIdentity ();
		$user = $this->userTable->getUserByEmail ( $identity );
		$user_id = $user ['id'];
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
	
	
		if ($request->isPost ()) {
				
			$category_id = ( int ) $request->getPost ( 'cat_id' );
			$articles = $request->getPost ( 'articles' );
				
			if (count ( $articles ) > 0) {
	
				foreach ( $articles as $sp ) {
					$member = new ArticleCategoryMember();
					$member->article_cat_id = $category_id;
					$member->article_id = $sp;
					$member->updated_by = $user_id;
						
					if ($this->articleCategoryMemberTable->isMember ( $sp, $category_id ) == false) {
						$this->articleCategoryMemberTable->add ( $member );
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
	
		$id = ( int ) $this->params ()->fromQuery ( 'cat_id' );
		$category = $this->articleCategoryTable->get ( $id );
	
		$articles = $this->articleTable->getUncategorizedArticlesOfUser( $user_id,0,0 );
	
		return new ViewModel ( array (
				'category' => $category,
				'articles' => $articles,
				'redirectUrl'=>$redirectUrl,
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
	public function setArticleTable($articleTable) {
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
	
	
	
}
