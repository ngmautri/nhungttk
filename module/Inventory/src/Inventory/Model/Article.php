<?php

namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class Article {
	public $id;
	public $name;
	public $description;
	public $keywords;
	
	public $type;
	public $code;
	public $barcode;
	
	public $created_on;
	public $created_by;
	public $status;
	public $visibility;
	public $remarks;
	public function exchangeArray($data) {
		
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->name = (! empty ( $data ['name'] )) ? $data ['name'] : null;
		$this->description = (! empty ( $data ['description'] )) ? $data ['description'] : null;
		$this->keywords = (! empty ( $data ['keywords'] )) ? $data ['keywords'] : null;
		
		$this->type = (! empty ( $data ['type'] )) ? $data ['type'] : null;
		$this->code = (! empty ( $data ['code'] )) ? $data ['code'] : null;
		$this->barcode = (! empty ( $data ['barcode'] )) ? $data ['barcode'] : null;
		
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		$this->created_by = (! empty ( $data ['created_by'] )) ? $data ['created_by'] : null;
		$this->status = (! empty ( $data ['status'] )) ? $data ['status'] : null;
		$this->visibility = (! empty ( $data ['visibility'] )) ? $data ['visibility'] : null;
		$this->remarks = (! empty ( $data ['remarks'] )) ? $data ['remarks'] : null;
	}
}

