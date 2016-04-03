<?php

namespace Procurement\Model;

/**
 *
 * @author nmt
 *        
 */
class PurchaseRequest {
	
	public $id;
	public $pr_number;
	public $name;
	public $description;
	
	public $requested_by;
	public $requested_on;

	public $verified_by;
	public $verified_on;
	
	public $approved_by;
	public $approved_on;
	
	public $released_by;
	public $released_on;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->pr_number = (! empty ( $data ['pr_number'] )) ? $data ['pr_number'] : null;
		$this->name = (! empty ( $data ['name'] )) ? $data ['name'] : null;		
		$this->description = (! empty ( $data ['description'] )) ? $data ['description'] : null;

		$this->requested_by = (! empty ( $data ['requested_by'] )) ? $data ['requested_by'] : null;
		$this->requested_on = (! empty ( $data ['requested_on'] )) ? $data ['requested_on'] : null;
		
		$this->verified_by = (! empty ( $data ['verified_by'] )) ? $data ['verified_by'] : null;
		$this->verified_on = (! empty ( $data ['verified_on'] )) ? $data ['verified_on'] : null;
		
		$this->approved_by= (! empty ( $data ['approved_by'] )) ? $data ['approved_by'] : null;
		$this->approved_on = (! empty ( $data ['approved_on'] )) ? $data ['approved_on'] : null;
		
		$this->released_by = (! empty ( $data ['released_by'] )) ? $data ['released_by'] : null;
		$this->released_on = (! empty ( $data ['released_on'] )) ? $data ['released_on'] : null;
		
	}
}

