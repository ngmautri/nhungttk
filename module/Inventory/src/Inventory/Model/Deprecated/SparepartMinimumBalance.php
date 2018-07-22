<?php

namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class SparepartMinimumBalance {
	public $id;
	public $sparepart_id;
	public $minimum_balance;
	public $remarks;
	public $created_by;	
	public $created_on;
	public $status;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->sparepart_id = (! empty ( $data ['sparepart_id'] )) ? $data ['sparepart_id'] : null;
		$this->minimum_balance = (! empty ( $data ['minimum_balance'] )) ? $data ['minimum_balance'] : null;
		$this->remarks = (! empty ( $data ['remarks'] )) ? $data ['remarks'] : null;
		$this->created_by = (! empty ( $data ['created_by'] )) ? $data ['created_by'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		$this->status = (! empty ( $data ['status'] )) ? $data ['status'] : null;
		
	}
}

