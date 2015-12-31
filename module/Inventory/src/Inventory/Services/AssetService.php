<?php

namespace Inventory\Services;


use Zend\Permissions\Acl\Acl;
use MLA\Files;
use MLA\Service\AbtractService;
use Zend\Mail\Message;

/**
 * 
 * @author nmt
 *
 */
class AssetService extends AbtractService
{
	
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \MLA\Service\AbtractService::initAcl()
	 */
	public function initAcl(Acl $acl){
		// TODO
	}
	
	
	/**
	 * 
	 * @param unknown $id
	 */
	public function createAssetFolderById($id) {
		try {
			
			
			
			$asset_folders_tpl = ROOT . DIRECTORY_SEPARATOR . "/module/Inventory/data" . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "asset_folder";
			
			$asset_dir = ROOT . DIRECTORY_SEPARATOR . "/module/Inventory/data" . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "asset_" . $id;
			
			// Setup SMTP transport using PLAIN authentication over TLS
			/* $transport = $this->getServiceLocator()->get('SmtpTransportService');
			$message = new Message ();
			$message->addTo ( 'ngmautri@outlook.com' )->addFrom ( 'mib-team@web.de' )->setSubject ( 'Mascot Laos' )->setBody ( "Asset foler " . $asset_dir );
				
			$transport->send ( $message ); */
			
			
			if (is_dir ( $asset_dir )) {
				Files::recursiveCopy ( $asset_folders_tpl, $asset_dir );
				return $asset_dir;
			} else {
				if (mkdir ( $asset_dir )) {
					Files::recursiveCopy ( $asset_folders_tpl, $asset_dir );
					return $asset_dir;
				}
			}
		} catch ( Exception $e ) {
			return null;
		}
	}
	
	/**
	 * 
	 * @param unknown $id
	 * @return NULL
	 */
	public function getRootDirById($id) {
		try {
			$company_dir = ROOT . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "asset_" . $id;
			return scandir ( $company_dir );
		} catch ( Exception $e ) {
			return null;
		}
	}
}