<?php

namespace Inventory\Model;

/**
 * 
 * @author nmt
 *
 */
class SparepartPurchasing {
	public $id;
	public $article_id;
	public $vendor_id;

	public $vendor_article_code;
	public $vendor_unit;
	public $vendor_unit_price;
	public $currency;
	public $price_valid_from;
	public $is_preferred;
	
	public $created_on;
	public $created_by;
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->article_id = (! empty ( $data ['article_id'] )) ? $data ['article_id'] : null;
		$this->vendor_id = (! empty ( $data ['vendor_id'] )) ? $data ['vendor_id'] : null;
		
			
		$this->vendor_article_code = (! empty ( $data ['vendor_article_code'] )) ? $data ['vendor_article_code'] : null;
		$this->vendor_unit = (! empty ( $data ['vendor_unit'] )) ? $data ['vendor_unit'] : null;
		$this->vendor_unit_price = (! empty ( $data ['vendor_unit_price'] )) ? $data ['vendor_unit_price'] : null;
		$this->currency = (! empty ( $data ['currency'] )) ? $data ['currency'] : null;
		$this->price_valid_from = (! empty ( $data ['price_valid_from'] )) ? $data ['price_valid_from'] : null;
		$this->is_preferred = (! empty ( $data ['is_preferred'] )) ? $data ['is_preferred'] : null;
		
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		$this->created_by = (! empty ( $data ['created_by'] )) ? $data ['created_by'] : null;
		
		
	}
}

