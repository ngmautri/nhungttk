<?php

namespace User\Service;

use User\Service\AbstractCategory;
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
	 * @see \User\Service\AbstractCategory::initCategory()
	 */
	public function initCategory(){
		$cats = $this->articleCategoryTable->getArticleCategories();
		
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
