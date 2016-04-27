<?php

namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class ArticleCategory {
	public $id;
	public $name;
	public $description;
	public $parent_id;	
	public $created_on;
	
	public $members;
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->name = (! empty ( $data ['name'] )) ? $data ['name'] : null;
		$this->description = (! empty ( $data ['description'] )) ? $data ['description'] : null;
		$this->parent_id = (! empty ( $data ['parent_id'] )) ? $data ['parent_id'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
	}
}

