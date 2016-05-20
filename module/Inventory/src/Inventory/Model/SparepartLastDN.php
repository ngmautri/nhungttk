<?php

namespace Inventory\Model;

/**
 * 
 * @author nmt
 *
 */
class SparepartLastDN{
	public $id;
	public $sparepart_id;
	public $last_workflow_id;
	
	public function exchangeArray($data) {
		
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->sparepart_id= (! empty ( $data ['sparepart_id'] )) ? $data ['sparepart_id'] : null;
		$this->last_workflow_id= (! empty ( $data ['last_workflow_id'] )) ? $data ['last_workflow_id'] : null;
		
	}
}

