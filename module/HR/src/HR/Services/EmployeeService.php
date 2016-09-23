<?php

namespace HR\Services;

use Zend\Permissions\Acl\Acl;
use MLA\Files;
use MLA\Service\AbstractService;
use Zend\Mail\Message;

/**
 *
 * @author nmt
 *        
 */
class EmployeeService extends AbstractService {
	public function initAcl(Acl $acl) {
		// TODO
	}
	
	/**
	 *
	 * @param unknown $id        	
	 * @return NULL
	 */
	public function getRootDirById($id) {
		try {
			$asset_dir = ROOT . DIRECTORY_SEPARATOR . "module" . DIRECTORY_SEPARATOR . "Inventory" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "articles" . DIRECTORY_SEPARATOR . "sparepart_" . $id;
			return scandir ( $asset_dir );
		} catch ( Exception $e ) {
			return null;
		}
	}
	
	/**
	 *
	 * @return string|NULL
	 */
	public function getPicturesPath() {
		try {
			$asset_dir = ROOT . DIRECTORY_SEPARATOR . "module" . DIRECTORY_SEPARATOR . "HR" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "employees" . DIRECTORY_SEPARATOR . "pictures";
			return $asset_dir;
		} catch ( Exception $e ) {
			return null;
		}
	}
}