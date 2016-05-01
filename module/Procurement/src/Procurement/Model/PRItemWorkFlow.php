<?php

namespace Procurement\Model;

/**
 *
 * @author nmt
 *        
 */
class PRItemWorkFlow {
	
	public $id;
	public $pr_item_id;
	public $delivery_id;
	public $status;
	
	public $updated_on;
	public $updated_by;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->pr_item_id = (! empty ( $data ['pr_item_id'] )) ? $data ['pr_item_id'] : null;
		$this->delivery_id = (! empty ( $data ['delivery_id'] )) ? $data ['delivery_id'] : null;
		
		$this->status = (! empty ( $data ['status'] )) ? $data ['status'] : null;		
		$this->updated_on = (! empty ( $data ['updated_on'] )) ? $data ['updated_on'] : null;
		$this->updated_by = (! empty ( $data ['updated_by'] )) ? $data ['updated_by'] : null;
		}
}

