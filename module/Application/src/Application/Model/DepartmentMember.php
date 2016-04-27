<?php

namespace Application\Model;

/**
 *
 * @author nmt
 *        
 */
class DepartmentMember {
	public $id;
	public $department_id;
	public $user_id;
	public $updated_by;

	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->department_id = (! empty ( $data ['department_id'] )) ? $data ['department_id'] : null;
		$this->user_id = (! empty ( $data ['user_id'] )) ? $data ['user_id'] : null;
		$this->updated_by = (! empty ( $data ['updated_by'] )) ? $data ['updated_by'] : null;
		
	}
}

