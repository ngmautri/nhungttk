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
	public $code;	
	public $unit;
	public $quantity;
	public $EDT;
	public $comment;
	public $created_on;
	
	public $sparepart_id;
	public $asset_id;
	public $other_res_id;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->purchase_request_id = (! empty ( $data ['purchase_request_id'] )) ? $data ['purchase_request_id'] : null;
		$this->priority = (! empty ( $data ['priority'] )) ? $data ['priority'] : null;
		$this->name = (! empty ( $data ['name'] )) ? $data ['name'] : null;
		$this->description = (! empty ( $data ['description'] )) ? $data ['description'] : null;
		$this->code = (! empty ( $data ['code'] )) ? $data ['code'] : null;
		
		$this->unit = (! empty ( $data ['unit'] )) ? $data ['unit'] : null;
		$this->quantity = (! empty ( $data ['quantity'] )) ? $data ['quantity'] : null;
		$this->EDT = (! empty ( $data ['EDT'] )) ? $data ['EDT'] : null;
		$this->comment = (! empty ( $data ['comment'] )) ? $data ['comment'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		
		$this->sparepart_id = (! empty ( $data ['sparepart_id'] )) ? $data ['sparepart_id'] : null;
		$this->asset_id = (! empty ( $data ['asset_id'] )) ? $data ['asset_id'] : null;
		$this->other_res_id = (! empty ( $data ['other_res_id'] )) ? $data ['other_res_id'] : null;
	}
}

