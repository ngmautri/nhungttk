<?php

namespace Procurement\Model;

/**
 *
 * @author nmt
 *        
 */
class PRItemSelfConfirmation {
	
	public $id;
	public $pr_item_id;
	public $status;
	
	public $updated_on;
	public $updated_by;
	
	public $confirmed_quantity;
	public $rejected_quantity;
	public $remarks;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
			$this->pr_item_id = (! empty ( $data ['pr_item_id'] )) ? $data ['pr_item_id'] : null;
	
	
		$this->status = (! empty ( $data ['status'] )) ? $data ['status'] : null;
		$this->updated_on = (! empty ( $data ['updated_on'] )) ? $data ['updated_on'] : null;
		$this->updated_by = (! empty ( $data ['updated_by'] )) ? $data ['updated_by'] : null;
	
		$this->confirmed_quantity = (! empty ( $data ['confirmed_quantity'] )) ? $data ['confirmed_quantity'] : null;
		$this->rejected_quantity = (! empty ( $data ['rejected_quantity'] )) ? $data ['rejected_quantity'] : null;
		$this->remarks = (! empty ( $data ['remarks'] )) ? $data ['remarks'] : null;
	}
}

