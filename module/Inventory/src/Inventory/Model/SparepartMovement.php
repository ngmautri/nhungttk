<?php

namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class SparepartMovement {
	public $id;
	public $movement_date;
	public $sparepart_id;
	public $asset_id;
	public $asset_name;
	public $flow;
	public $quantity;
	public $reason;
	public $requester;
	public $comment;
	public $created_on;
	
	public $wh_id;
	public $movement_type;
	public $asset_location;
	
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->movement_date = (! empty ( $data ['sparepart_id'] )) ? $data ['movement_date'] : null;
		$this->sparepart_id = (! empty ( $data ['sparepart_id'] )) ? $data ['sparepart_id'] : null;
		
		$this->asset_id = (! empty ( $data ['asset_id'] )) ? $data ['asset_id'] : null;
		$this->asset_name = (! empty ( $data ['asset_name'] )) ? $data ['asset_name'] : null;
		
		$this->flow = (! empty ( $data ['flow'] )) ? $data ['flow'] : null;
		$this->quantity = (! empty ( $data ['quantity'] )) ? $data ['quantity'] : null;
		
		$this->reason = (! empty ( $data ['reason'] )) ? $data ['reason'] : null;
		$this->requester = (! empty ( $data ['requester'] )) ? $data ['requester'] : null;
		$this->comment = (! empty ( $data ['comment'] )) ? $data ['comment'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		
		$this->wh_id = (! empty ( $data ['comment'] )) ? $data ['wh_id'] : null;
		$this->movement_type = (! empty ( $data ['created_on'] )) ? $data ['movement_type'] : null;
		$this->asset_location = (! empty ( $data ['asset_location'] )) ? $data ['asset_location'] : null;
		
	}
}

