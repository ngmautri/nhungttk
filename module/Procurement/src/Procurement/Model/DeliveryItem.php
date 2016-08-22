<?php

namespace Procurement\Model;

/**
 *
 * @author nmt
 *	the procurement staff will receive the goods, then delivery /notify to requester
 *        
 */
class DeliveryItem {
	
	public $id;
	public $receipt_date;
	public $delivery_date;

	public $delivery_id;
	public $po_item_id;
	public $pr_item_id;
	
	public $name;
	public $code;
	public $unit;

	public $delivered_quantity;
	public $price;
	public $currency;
	public $payment_method;
	public $vendor_id;

	public $remarks;
	
	public $created_by;
	public $created_on;
	
	public $last_workflow_id;
	
	public $invoice_no;
	public $invoice_date;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->receipt_date = (! empty ( $data ['receipt_date'] )) ? $data ['receipt_date'] : null;
		$this->delivery_date = (! empty ( $data ['delivery_date'] )) ? $data ['delivery_date'] : null;
		
		
		$this->delivery_id = (! empty ( $data ['delivery_id'] )) ? $data ['delivery_id'] : null;
		$this->po_item_id = (! empty ( $data ['po_item_id'] )) ? $data ['po_item_id'] : null;
		$this->pr_item_id = (! empty ( $data ['pr_item_id'] )) ? $data ['pr_item_id'] : null;
		
		$this->name = (! empty ( $data ['name'] )) ? $data ['name'] : null;
		$this->code = (! empty ( $data ['code'] )) ? $data ['code'] : null;
		$this->unit = (! empty ( $data ['unit'] )) ? $data ['unit'] : null;
		
		$this->delivered_quantity = (! empty ( $data ['delivered_quantity'] )) ? $data ['delivered_quantity'] : null;
		$this->price= (! empty ( $data ['price'] )) ? $data ['price'] : null;
		$this->currency = (! empty ( $data ['currency'] )) ? $data ['currency'] : null;
		$this->vendor_id = (! empty ( $data ['vendor_id'] )) ? $data ['vendor_id'] : null;

		$this->remarks = (! empty ( $data ['remarks'] )) ? $data ['remarks'] : null;
		
		$this->created_by = (! empty ( $data ['created_by'] )) ? $data ['created_by'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;

		$this->last_workflow_id= (! empty ( $data ['last_workflow_id'] )) ? $data ['last_workflow_id'] : null;
		
		$this->invoice_no = (! empty ( $data ['invoice_no'] )) ? $data ['invoice_no'] : null;
		$this->invoice_date = (! empty ( $data ['invoice_date'] )) ? $data ['invoice_date'] : null;
		
		
	}
}

