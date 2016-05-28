<?php

namespace User\Model;

/**
 *
 * @author nmt
 *        
 */
class AclWhiteList {
	public $id;
	public $resource_id;
	public $status;
	public $created_on;
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->resource_id = (! empty ( $data ['resource_id'] )) ? $data ['resource_id'] : null;
		$this->status = (! empty ( $data ['resource'] )) ? $data ['type'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
	}
}

