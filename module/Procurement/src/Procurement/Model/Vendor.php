<?php

namespace Procurement\Model;

/**
 *
 * @author nmt
 *        
 */
class Vendor {
	
	public $id;
	public $name;
	public $keywords;
	public $status;
	public $created_on;
	public $created_by;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->name = (! empty ( $data ['name'] )) ? $data ['name'] : null;
		$this->keywords = (! empty ( $data ['keywords'] )) ? $data ['keywords'] : null;
		$this->status = (! empty ( $data ['status'] )) ? $data ['status'] : null;		
		$this->created_by = (! empty ( $data ['created_by'] )) ? $data ['created_by'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		
	}
}

