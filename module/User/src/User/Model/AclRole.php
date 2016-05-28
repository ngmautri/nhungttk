<?php

namespace User\Model;

/**
 *
 * @author nmt
 *        
 */
class AclRole {
	public $id;
	public $role;
	public $parent_id;
	public $path;
	public $path_depth;
	public $status;
	public $remarks;
	public $created_on;
	public $created_by;
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->role = (! empty ( $data ['role'] )) ? $data ['role'] : null;
		$this->parent_id = (! empty ( $data ['parent_id'] )) ? $data ['parent_id'] : null;
		$this->path = (! empty ( $data ['path'] )) ? $data ['path'] : null;
		$this->path_depth = (! empty ( $data ['path_depth'] )) ? $data ['path_depth'] : null;
		
		
		$this->status = (! empty ( $data ['status'] )) ? $data ['status'] : null;
		$this->remarks = (! empty ( $data ['remarks'] )) ? $data ['remarks'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		$this->created_by = (! empty ( $data ['created_by'] )) ? $data ['created_by'] : null;
		
	}
}

