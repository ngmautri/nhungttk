<?php

namespace Application\Model;

/**
 *
 * @author nmt
 *        
 */
class UOM {
	public $id;
	public $uom_name;
	public $uom_description;
	public $status;
	public $created_on;
	public $created_by;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->uom_name = (! empty ( $data ['uom_name'] )) ? $data ['uom_name'] : null;
		$this->uom_description = (! empty ( $data ['uom_description'] )) ? $data ['uom_description'] : null;
	
		$this->status = (! empty ( $data ['status'] )) ? $data ['status'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		$this->created_by = (! empty ( $data ['created_by'] )) ? $data ['created_by'] : null;
	}
}

