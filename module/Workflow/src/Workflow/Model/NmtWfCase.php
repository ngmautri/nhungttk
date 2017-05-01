<?php

namespace Workflow\Model;

/**
 *
 * @author nmt
 *        
 */
class NmtWfCase{
	public $case_id;
	public $workflow_id;
	public $context;
	public $case_status;
	public $start_date;
	public $end_date;
	public $case_created_by;
	public $case_created_on;
	
	public function exchangeArray($data) {
		$this->case_id = (! empty ( $data ['case_id'] )) ? $data ['case_id'] : null;
		$this->workflow_id = (! empty ( $data ['workflow_id'] )) ? $data ['workflow_id'] : null;
		$this->context = (! empty ( $data ['context'] )) ? $data ['context'] : null;
		$this->case_status = (! empty ( $data ['case_status'] )) ? $data ['case_status'] : null;
		$this->start_date = (! empty ( $data ['start_date'] )) ? $data ['start_date'] : null;
		$this->end_date = (! empty ( $data ['end_date'] )) ? $data ['end_date'] : null;
		$this->case_created_by = (! empty ( $data ['case_created_by'] )) ? $data ['case_created_by'] : null;
		$this->case_created_on = (! empty ( $data ['case_created_on'] )) ? $data ['case_created_on'] : null;
		}
}

