<?php

namespace Procurement\Model;

/**
 *
 * @author nmt
 *        
 */
class PRWorkFlow {
	
	public $id;
	public $purchase_request_id;
	public $status;
	
	public $updated_on;
	public $updated_by;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->purchase_request_id = (! empty ( $data ['$purchase_request_id'] )) ? $data ['$purchase_request_id'] : null;
		$this->status = (! empty ( $data ['status'] )) ? $data ['status'] : null;		
		$this->updated_on = (! empty ( $data ['updated_on'] )) ? $data ['updated_on'] : null;
		$this->updated_by = (! empty ( $data ['updated_by'] )) ? $data ['updated_by'] : null;
		}
}

