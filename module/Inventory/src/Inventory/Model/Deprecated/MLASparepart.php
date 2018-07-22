<?php

namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class MLASparepart {
	
	public $id;
	public $name;
	public $name_local;
	public $description;
	
	public $code;
	public $tag;
	public $location;
	
	//Picture  object)
	public $pictures;
	
	public $spareparts;
	public $comment;
	public $created_on;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->name = (! empty ( $data ['name'] )) ? $data ['name'] : null;
		$this->name_local = (! empty ( $data ['name_local'] )) ? $data ['name_local'] : null;
		$this->description = (! empty ( $data ['description'] )) ? $data ['description'] : null;
		
		$this->code = (! empty ( $data ['code'] )) ? $data ['code'] : null;
		$this->tag = (! empty ( $data ['tag'] )) ? $data ['tag'] : null;
		$this->location = (! empty ( $data ['location'] )) ? $data ['location'] : null;
		
		$this->pictures = (! empty ( $data ['pictures'] )) ? $data ['pictures'] : null;
		
		$this->comment = (! empty ( $data ['comment'] )) ? $data ['comment'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
	}
	
}

