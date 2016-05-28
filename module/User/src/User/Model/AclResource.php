<?php

namespace User\Model;

/**
 *
 * @author nmt
 *        
 */
class AclResource {
	public $id;
	public $resource;
	public $type;
	public $remarks;
	public $created_on;
	public $updated_on;
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->resource = (! empty ( $data ['resource'] )) ? $data ['resource'] : null;
		$this->type = (! empty ( $data ['resource'] )) ? $data ['type'] : null;
		$this->remarks = (! empty ( $data ['remarks'] )) ? $data ['remarks'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		$this->updated_on = (! empty ( $data ['updated_on'] )) ? $data ['updated_on'] : null;
	}
}

