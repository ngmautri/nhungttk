<?php

namespace Procurement\Model;

/**
 *
 * @author nmt
 *        
 */
class PurchaseRequestItem{
	public $id;
	public $purchase_request_id;
	public $priority;
	public $name;
	public $description;
	public $unit;
	public $quantity;
	public $EDT;
	public $comment;
	public $created_on;
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->purchase_request_id = (! empty ( $data ['purchase_request_id'] )) ? $data ['purchase_request_id'] : null;
		$this->priority = (! empty ( $data ['priority'] )) ? $data ['priority'] : null;
		$this->name = (! empty ( $data ['name'] )) ? $data ['name'] : null;
		$this->description = (! empty ( $data ['description'] )) ? $data ['description'] : null;
		$this->unit = (! empty ( $data ['unit'] )) ? $data ['unit'] : null;
		$this->quantity = (! empty ( $data ['quantity'] )) ? $data ['quantity'] : null;
		$this->EDT = (! empty ( $data ['EDT'] )) ? $data ['EDT'] : null;
		$this->comment = (! empty ( $data ['comment'] )) ? $data ['comment'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		
	}
}

