<?php

namespace Procurement\Model;

/**
 *
 * @author nmt
 *        
 */
class Delivery {
	
	public $id;
	public $dn_number;
	public $description;
	public $created_by;
	public $created_on;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->dn_number = (! empty ( $data ['dn_number'] )) ? $data ['dn_number'] : null;
		$this->description = (! empty ( $data ['description'] )) ? $data ['description'] : null;
		$this->created_by = (! empty ( $data ['created_by'] )) ? $data ['created_by'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		
	}
}

