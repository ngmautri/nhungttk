<?php

namespace Procurement\Model;

/**
 *
 * @author nmt
 *        
 */
class DeliveryItem {
	
	public $id;
	public $delivery_id;
	public $pr_item_id;
	
	public $name;
	public $code;
	
	public $unit;
	public $delivered_quantity;

	public $price;
	public $currency;
	
	public $vendor_id;
	public $notes;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->delivery_id = (! empty ( $data ['delivery_id'] )) ? $data ['delivery_id'] : null;
		$this->pr_item_id = (! empty ( $data ['pr_item_id'] )) ? $data ['pr_item_id'] : null;
		
		$this->name = (! empty ( $data ['name'] )) ? $data ['name'] : null;
		$this->code = (! empty ( $data ['code'] )) ? $data ['code'] : null;
		$this->unit = (! empty ( $data ['unit'] )) ? $data ['unit'] : null;
		$this->delivered_quantity = (! empty ( $data ['delivered_quantity'] )) ? $data ['delivered_quantity'] : null;
		
		$this->price= (! empty ( $data ['price'] )) ? $data ['price'] : null;
		$this->currency = (! empty ( $data ['currency'] )) ? $data ['currency'] : null;
		
		$this->vendor_id = (! empty ( $data ['vendor_id'] )) ? $data ['vendor_id'] : null;
		$this->notes = (! empty ( $data ['notes'] )) ? $data ['notes'] : null;
		
	}
}

