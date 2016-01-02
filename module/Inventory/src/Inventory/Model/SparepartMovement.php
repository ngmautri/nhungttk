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
	public $flow;
	public $quantity;
	public $reason;
	public $requester;
	public $comment;
	public $created_on;
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->movement_date = (! empty ( $data ['sparepart_id'] )) ? $data ['movement_date'] : null;
		$this->sparepart_id = (! empty ( $data ['sparepart_id'] )) ? $data ['sparepart_id'] : null;
		
		$this->asset_id = (! empty ( $data ['asset_id'] )) ? $data ['asset_id'] : null;
		$this->flow = (! empty ( $data ['flow'] )) ? $data ['flow'] : null;
		$this->quantity = (! empty ( $data ['quantity'] )) ? $data ['quantity'] : null;
		
		$this->reason = (! empty ( $data ['reason'] )) ? $data ['reason'] : null;
		$this->requester = (! empty ( $data ['requester'] )) ? $data ['requester'] : null;
		$this->comment = (! empty ( $data ['comment'] )) ? $data ['comment'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
	}
}

