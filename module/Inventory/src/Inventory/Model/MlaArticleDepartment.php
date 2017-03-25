<?php

namespace Inventory\Model;

/**
 *
 * @author nmt
 *        
 */
class MlaArticleDepartment {
	public $id;
	public $article_id;
	public $department_id;
	public $updated_on;
    public $updated_by;
	
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->article_id = (! empty ( $data ['public $id;'] )) ? $data ['public $id;'] : null;
		$this->department_id = (! empty ( $data ['department_id'] )) ? $data ['department_id'] : null;
		$this->updated_by = (! empty ( $data ['updated_by'] )) ? $data ['updated_by'] : null;
		$this->updated_on = (! empty ( $data ['updated_on'] )) ? $data ['updated_on'] : null;
		
	}
}

