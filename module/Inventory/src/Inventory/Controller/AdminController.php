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


class AdminController extends AbstractActionController {
	protected $sparePartCategoryTable;
	protected $sparePartCategoryMemberTable;
	protected $sparepartTable;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	public function sparepartCategoryAction() {
		$categories = $this->sparePartCategoryTable->fetchAll ();
		return new ViewModel ( array (
				'categories' => $categories 
		) );
	}
	public function editSparepartCategoryAction() {
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$category = new SparepartCategory ();
			$category->name = $request->getPost ( 'name' );
			$category->description = $request->getPost ( 'description' );
			$category->parent_id = $request->getPost ( 'parent_id' );
			
			$this->sparePartCategoryTable->update ( $category, $request->getPost ( 'id' ) );
			
			return $this->redirect ()->toRoute ( 'assetcategory' );
		} else {
			$id = ( int ) $this->params ()->fromQuery ( 'id' );
			$category = $this->sparePartCategoryTable->get ( $id );
			
			return new ViewModel ( array (
					'category' => $category 
			) );
		}
	}
	
	public function addSparepartCategoryAction() {
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			
			$category = new SparepartCategory ();
			$category->name = $request->getPost ( 'name' );
			$category->description = $request->getPost ( 'description' );
			$category->parent_id = $request->getPost ( 'parent_id' );
			$this->sparePartCategoryTable->add ( $category );
			
			return $this->redirect ()->toRoute ( 'assetcategory' );
		}
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
}
