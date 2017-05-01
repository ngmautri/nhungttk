<?php

namespace Workflow\Model;

/**
 *
 * @author nmt
 *        
 */
class NmtWfWorkitem {
	public $workflow_id;
	public $node_id;
	public $node_name;
	public $node_parent_id;
	public $node_type;
	public $node_connection_type;
	public $path;
	public $path_depth;
	public $status;
	public $remarks;
	public $node_created_on;
	public $node_created_by;
	public $place_id;
	public $transition_id;
	
	public function exchangeArray($data) {
		$this->workflow_id = (! empty ( $data ['workflow_id'] )) ? $data ['workflow_id'] : null;
		$this->node_id = (! empty ( $data ['node_id'] )) ? $data ['node_id'] : null;
		$this->node_name = (! empty ( $data ['node_name'] )) ? $data ['node_name'] : null;
		$this->node_parent_id = (! empty ( $data ['node_parent_id'] )) ? $data ['node_parent_id'] : null;
		$this->node_type = (! empty ( $data ['node_type'] )) ? $data ['node_type'] : null;
		$this->node_connection_type = (! empty ( $data ['node_connection_type'] )) ? $data ['node_connection_type'] : null;
		$this->path = (! empty ( $data ['path'] )) ? $data ['path'] : null;
		$this->path_depth = (! empty ( $data ['path_depth'] )) ? $data ['path_depth'] : null;
		$this->status = (! empty ( $data ['status'] )) ? $data ['status'] : null;
		
		$this->remarks = (! empty ( $data ['remarks'] )) ? $data ['remarks'] : null;
		$this->node_created_on = (! empty ( $data ['node_created_on'] )) ? $data ['node_created_on'] : null;
		$this->node_created_by = (! empty ( $data ['node_created_by'] )) ? $data ['node_created_by'] : null;
		$this->place_id = (! empty ( $data ['place_id'] )) ? $data ['place_id'] : null;
		$this->transition_id = (! empty ( $data ['transition_id'] )) ? $data ['transition_id'] : null;
		
		
	
	}
}

