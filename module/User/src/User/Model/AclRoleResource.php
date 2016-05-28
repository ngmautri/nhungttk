<?php

namespace User\Model;

/**
 *
 * @author nmt
 *        
 */
class AclRoleResource {
	public $id;
	public $role_id;
	public $resource_id;
		
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->role_id = (! empty ( $data ['role_id'] )) ? $data ['role_id'] : null;
		$this->resource_id = (! empty ( $data ['resource_id'] )) ? $data ['resource_id'] : null;
	}
}

