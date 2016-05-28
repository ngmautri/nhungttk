<?php

namespace User\Model;

/**
 *
 * @author nmt
 *        
 */
class AclUserRole {
	public $id;
	public $role_id;
	public $user_id;
	public $updated_by;
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->role_id = (! empty ( $data ['role_id'] )) ? $data ['role_id'] : null;
		$this->user_id = (! empty ( $data ['user_id'] )) ? $data ['user_id'] : null;
		$this->updated_by = (! empty ( $data ['updated_by'] )) ? $data ['updated_by'] : null;
	}
}

