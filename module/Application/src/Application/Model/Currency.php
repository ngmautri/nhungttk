<?php

namespace Application\Model;

/**
 *
 * @author nmt
 *        
 */
class Currency {
	public $id;
	public $currency;
	public $description;
	public $status;
	public $created_on;
	public $created_by;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->currency = (! empty ( $data ['currency'] )) ? $data ['currency'] : null;
		$this->description = (! empty ( $data ['description'] )) ? $data ['description'] : null;
	
		$this->status = (! empty ( $data ['status'] )) ? $data ['status'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		$this->created_by = (! empty ( $data ['created_by'] )) ? $data ['created_by'] : null;
	}
}

