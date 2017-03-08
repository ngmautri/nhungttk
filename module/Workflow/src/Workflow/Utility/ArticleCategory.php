<?php

namespace User\Utility;

use User\Utility\AbstractCategory;
use Inventory\Model\ArticleCategoryTable;

/**
 *
 * @author nmt
 *        
 */
class ArticleCategory extends AbstractCategory{
	
	protected $articleCategoryTable;
	
	/**
	 * To set up data and index 
	 
	 * {@inheritDoc}
	 * @see \User\Utility\AbstractCategory::initCategory()
	 */
	public function initCategory(){
		$cats = $this->articleCategoryTable->fetchAll();
		
		foreach ($cats as $row)
		{
			$id = $row->id;
			$parent_id = $row->parent_id;
			$this->data[$id] = $row;
			$this->index[$parent_id][] = $id;
		}
		return $this;
	}
	
	
	public function getArticleCategoryTable() {
		return $this->articleCategoryTable;
	}
	public function setArticleCategoryTable(ArticleCategoryTable $articleCategoryTable) {
		$this->articleCategoryTable = $articleCategoryTable;
		return $this;
	}
	
}
