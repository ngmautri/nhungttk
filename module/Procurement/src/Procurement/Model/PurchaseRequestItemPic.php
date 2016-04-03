<?php

namespace Procurement\Model;

/**
 *
 * @author nmt
 *        
 */
class PurchaseRequestItemPic{
	public $id;
	public $category;
	public $created_on;
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->category = (! empty ( $data ['category'] )) ? $data ['category'] : null;
		$this->description = (! empty ( $data ['description'] )) ? $data ['description'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
	}
}

