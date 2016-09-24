<?php

namespace HR\Model;

/**
 *
 * @author nmt
 *        
 */
class Employee {
	
	public $id;
	public $employee_code;
	public $employee_name;
	public $employee_name_local;
	public $employee_dob;
	public $employee_gender;
	public $employee_last_status_id;
	public $created_on;
	public $created_by;
	public $remarks;
	
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->employee_code = (! empty ( $data ['employee_code'] )) ? $data ['employee_code'] : null;
		$this->employee_name = (! empty ( $data ['employee_name'] )) ? $data ['employee_name'] : null;
		$this->employee_name_local = (! empty ( $data ['employee_name_local'] )) ? $data ['employee_name_local'] : null;
	
		$this->employee_dob = (! empty ( $data ['employee_dob'] )) ? $data ['employee_dob'] : null;
		$this->employee_last_status_id = (! empty ( $data ['employee_last_status_id'] )) ? $data ['employee_last_status_id'] : null;
		$this->employee_gender = (! empty ( $data ['employee_gender'] )) ? $data ['employee_gender'] : null;
		
		$this->created_by = (! empty ( $data ['created_by'] )) ? $data ['created_by'] : null;
		$this->created_on = (! empty ( $data ['created_on'] )) ? $data ['created_on'] : null;
		
		$this->remarks = (! empty ( $data ['remarks'] )) ? $data ['remarks'] : null;
		
	}
}

