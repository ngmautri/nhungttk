<?php

namespace Workflow\Model;

/**
 *
 * @author nmt
 *        
 */
class NmtWfToken{
	public $token_id;
	public $case_id;
	public $place_id;
	public $workflow_id;
	public $token_status;
	public $enabled_date;
	public $token_enabled_by;
	public $cancelled_date;
	public $token_cancelled_by;
	public $comsumed_date;
	public $token_consumed_by;
	public $node_id;
		
	public function exchangeArray($data) {
		$this->token_id = (! empty ( $data ['token_id'] )) ? $data ['token_id'] : null;
		$this->case_id = (! empty ( $data ['case_id'] )) ? $data ['case_id'] : null;
		$this->place_id = (! empty ( $data ['place_id'] )) ? $data ['place_id'] : null;
		$this->workflow_id = (! empty ( $data ['workflow_id'] )) ? $data ['workflow_id'] : null;
		$this->token_status = (! empty ( $data ['token_status'] )) ? $data ['token_status'] : null;
		$this->enabled_date = (! empty ( $data ['enabled_date'] )) ? $data ['enabled_date'] : null;
		$this->token_enabled_by = (! empty ( $data ['token_enabled_by'] )) ? $data ['token_enabled_by'] : null;
		$this->cancelled_date = (! empty ( $data ['cancelled_date'] )) ? $data ['cancelled_date'] : null;
		$this->token_cancelled_by = (! empty ( $data ['token_cancelled_by'] )) ? $data ['token_cancelled_by'] : null;
		$this->comsumed_date = (! empty ( $data ['comsumed_date'] )) ? $data ['comsumed_date'] : null;
		$this->token_consumed_by = (! empty ( $data ['token_consumed_by'] )) ? $data ['token_consumed_by'] : null;
		$this->node_id = (! empty ( $data ['node_id'] )) ? $data ['node_id'] : null;
		
	}
}

