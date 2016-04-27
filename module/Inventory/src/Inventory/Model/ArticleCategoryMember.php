<?php

namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class ArticleCategoryMember {
	public $id;
	public $article_id;
	public $article_cat_id;

	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->article_id = (! empty ( $data ['article_id'] )) ? $data ['article_id'] : null;
		$this->article_cat_id = (! empty ( $data ['article_cat_id'] )) ? $data ['article_cat_id'] : null;
	}
}

