<?php

namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class MLAAsset {
	
	public $id;
	public $name;
	public $description;
	
	public $category_id;
	public $group_id;
	
	public $tag;
	
	public $brand;
	public $model;
	public $serial;
	public $origin;
	
	public $received_on;
	
	
	
	//Picture  object)
	public $pictures;
	public $spareparts;
	public $vendor;
	public $location;
	public $status;
	
	
	public $comment;
	public $created_on;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->name = (! empty ( $data ['name'] )) ? $data ['name'] : null;
		$this->description = (! empty ( $data ['description'] )) ? $data ['description'] : null;
		$this->category_id = (! empty ( $data ['category_id'] )) ? $data ['category_id'] : null;
		$this->group_id = (! empty ( $data ['group_id'] )) ? $data ['group_id'] : null;
		
		$this->tag = (! empty ( $data ['tag'] )) ? $data ['tag'] : null;
		$this->brand = (! empty ( $data ['brand'] )) ? $data ['brand'] : null;
		$this->model = (! empty ( $data ['model'] )) ? $data ['model'] : null;
		$this->serial = (! empty ( $data ['serial'] )) ? $data ['serial'] : null;
		$this->origin = (! empty ( $data ['origin'] )) ? $data ['origin'] : null;
		$this->received_on = (! empty ( $data ['received_on'] )) ? $data ['received_on'] : null;
		
		$this->pictures = (! empty ( $data ['pictures'] )) ? $data ['pictures'] : null;
		$this->spareparts = (! empty ( $data ['spareparts'] )) ? $data ['spareparts'] : null;
		$this->vendors = (! empty ( $data ['vendors'] )) ? $data ['vendors'] : null;
		$this->location = (! empty ( $data ['location'] )) ? $data ['location'] : null;
		$this->status = (! empty ( $data ['status'] )) ? $data ['status'] : null;
		$this->comment = (! empty ( $data ['comment'] )) ? $data ['comment'] : null;
		
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
	}
	
}

