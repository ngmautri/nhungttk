<?php

namespace Application\Model;

/**
 *
 * @author nmt
 *        
 */
class Department {
	public $id;
	public $name;
	public $description;
	public $status;
	public $created_on;
	public $created_by;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->name = (! empty ( $data ['name'] )) ? $data ['name'] : null;
		$this->description = (! empty ( $data ['description'] )) ? $data ['description'] : null;
		$this->status = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		$this->created_by = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
	}
}

