<?php

namespace Procurement\Model;

/**
 *
 * @author nmt
 *        
 */
class  POItem {
	
	public $id;
	public $po_id;
	public $pr_item_id;
	
	public $name;
	public $code;
	public $unit;
	
	public $price;
	public $currency;
	public $payment_method;
	public $vendor_id;
	
	public $remarks;
	
	public $created_by;
	public $created_on;
	
	public $status;
		
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->po_id = (! empty ( $data ['po_id'] )) ? $data ['po_id'] : null;
		$this->pr_item_id = (! empty ( $data ['pr_item_id'] )) ? $data ['pr_item_id'] : null;
	
		$this->name = (! empty ( $data ['name'] )) ? $data ['name'] : null;
		$this->code = (! empty ( $data ['code'] )) ? $data ['code'] : null;
		$this->unit = (! empty ( $data ['unit'] )) ? $data ['unit'] : null;
		$this->price= (! empty ( $data ['price'] )) ? $data ['price'] : null;
		$this->currency = (! empty ( $data ['currency'] )) ? $data ['currency'] : null;
		$this->vendor_id = (! empty ( $data ['vendor_id'] )) ? $data ['vendor_id'] : null;
	
		$this->remarks = (! empty ( $data ['remarks'] )) ? $data ['remarks'] : null;
	
		$this->created_by = (! empty ( $data ['created_by'] )) ? $data ['created_by'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
	
		$this->status= (! empty ( $data ['status'] )) ? $data ['status'] : null;
	}
}

