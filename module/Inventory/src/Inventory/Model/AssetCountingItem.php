<?php

namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class AssetCountingItem {
	public $id;	
	public $counting_id;
	public $asset_id;
	
	public $location;
	public $counted_by;
	public $verified_by;
	
	public $acknowledged_by;
	public $counted_on;
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
	
		$this->counting_id = (! empty ( $data ['counting_id'] )) ? $data ['counting_id'] : null;
		$this->asset_id = (! empty ( $data ['asset_id'] )) ? $data ['asset_id'] : null;
		
		$this->counted_by = (! empty ( $data ['counted_by'] )) ? $data ['counted_by'] : null;
		$this->verified_by = (! empty ( $data ['verified_by'] )) ? $data ['verified_by'] : null;
		
		$this->acknowledged_by = (! empty ( $data ['acknowledged_by'] )) ? $data ['acknowledged_by'] : null;
		$this->counted_on = (! empty ( $data ['counted_on'] )) ? $data ['counted_on'] : null;
	}
}

