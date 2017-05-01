<?php

namespace Workflow\Model;

/**
 *
 * @author nmt
 *        
 */
class NmtWfPlace {
	
	public $workflow_id;
	public $place_id;
	public $place_type;
	public $place_name;
	public $place_description;
	public $place_created_by;
	public $place_created_on;
	
	public function exchangeArray($data) {
		$this->workflow_id = (! empty ( $data ['workflow_id'] )) ? $data ['workflow_id'] : null;
		$this->place_id = (! empty ( $data ['place_id'] )) ? $data ['place_id'] : null;
		$this->place_type= (! empty ( $data ['place_type'] )) ? $data ['place_type'] : null;
		$this->place_name = (! empty ( $data ['place_name'] )) ? $data ['place_name'] : null;
		$this->place_description = (! empty ( $data ['place_description'] )) ? $data ['place_description'] : null;
		$this->place_created_by = (! empty ( $data ['place_created_by'] )) ? $data ['place_created_by'] : null;
		$this->place_created_on = (! empty ( $data ['place_created_on'] )) ? $data ['place_created_on'] : null;
	}
}

