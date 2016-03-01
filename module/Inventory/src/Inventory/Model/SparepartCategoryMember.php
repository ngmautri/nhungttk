<?php

namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class SparepartCategoryMember {
	public $id;
	public $sparepart_id;
	public $sparepart_cat_id;

	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->sparepart_id = (! empty ( $data ['sparepart_id'] )) ? $data ['sparepart_id'] : null;
		$this->sparepart_cat_id = (! empty ( $data ['sparepart_cat_id'] )) ? $data ['sparepart_cat_id'] : null;
	}
}

