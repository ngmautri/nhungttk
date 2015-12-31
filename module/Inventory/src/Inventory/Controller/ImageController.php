<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Inventory\Model\AssetPicture;

class ImageController extends AbstractActionController {
	
	public $userTable;
	public $authService;
	public $massage = 'NULL';
	public $assetPictureTable;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		
		$request = $this->getRequest ();
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		
		$pic = $this->getAssetPictureTable ()->get( $id);
		
		$response = $this->getResponse();
		
		$imageContent =  file_get_contents($pic->url);
		$response->setContent($imageContent);
		$response->getHeaders()
		->addHeaderLine('Content-Transfer-Encoding', 'binary')
		->addHeaderLine('Content-Type', 'image/jpeg')
		->addHeaderLine('Content-Length', mb_strlen($imageContent));
		
		return $response;
		
	}
	// get AssetPictureTable
	private function getAssetPictureTable() {
		if (! $this->assetPictureTable) {
			$sm = $this->getServiceLocator ();
			$this->assetPictureTable = $sm->get ( 'Inventory\Model\AssetPictureTable' );
		}
		return $this->assetPictureTable;
	}
}



