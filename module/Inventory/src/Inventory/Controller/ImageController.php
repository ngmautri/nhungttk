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

class ImageController extends AbstractActionController {
	
	public $userTable;
	public $authService;
	public $massage = 'NULL';
	public $assetPictureTable;
	public $sparepartPictureTable;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		
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
	
	/*
	 * Defaul Action
	 */
	public function sparepartAction() {	
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
	
		$pic = $this->getSparepartPictureTable()->get( $id);
	
		$response = $this->getResponse();
	
		$imageContent =  file_get_contents($pic->url);
		$response->setContent($imageContent);
		$response->getHeaders()
		->addHeaderLine('Content-Transfer-Encoding', 'binary')
		->addHeaderLine('Content-Type', 'image/jpeg')
		->addHeaderLine('Content-Length', mb_strlen($imageContent));	
		return $response;
	}
	
	public function sparepartThumbnail200Action() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
	
		$pic = $this->getSparepartPictureTable()->get( $id);
	
		$response = $this->getResponse();
	
		$imageContent =  file_get_contents($pic->folder.DIRECTORY_SEPARATOR.'thumbnail_200_'.$pic->filename);
		$response->setContent($imageContent);
		$response->getHeaders()
		->addHeaderLine('Content-Transfer-Encoding', 'binary')
		->addHeaderLine('Content-Type', 'image/png')
		->addHeaderLine('Content-Length', mb_strlen($imageContent));
		return $response;
	}
	
	
	public function assetThumbnail150Action() {
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$pic = $this->getAssetPictureTable ()->get( $id);
		$response = $this->getResponse();
		$imageContent =  file_get_contents($pic->folder.DIRECTORY_SEPARATOR.'thumbnail_150_'.$pic->filename);
		$response->setContent($imageContent);
		$response->getHeaders()
		->addHeaderLine('Content-Transfer-Encoding', 'binary')
		->addHeaderLine('Content-Type', 'image/jpeg')
		->addHeaderLine('Content-Length', mb_strlen($imageContent));
		
		return $response;		
	}
	
	public function deleteSparePartPictureAction() {
		
		$request = $this->getRequest ();
	
		if ($request->isPost ()) {
			$del = $request->getPost ( 'delete_confirmation', 'NO' );
				
			if ($del === 'YES') {
				
				$id = ( int ) $request->getPost ( 'id' );
				$pic= $this->getSparepartPictureTable ()->get ( $id );
				$filename = $pic->url;
				
				if (file_exists($filename)) {
					unlink($filename);
				}
				$this->getSparepartPictureTable ()->delete ($id);
				
			}
			return $this->redirect ()->toRoute ( 'assetcategory' );
		}
	
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$pic= $this->getSparepartPictureTable ()->get ( $id );
		
		
	
		return new ViewModel ( array (
				'picture' => $pic
		) );
	}
	
	
	// get AssetPictureTable
	private function getAssetPictureTable() {
		if (! $this->assetPictureTable) {
			$sm = $this->getServiceLocator ();
			$this->assetPictureTable = $sm->get ( 'Inventory\Model\AssetPictureTable' );
		}
		return $this->assetPictureTable;
	}
	
	
	private function getSparepartPictureTable() {
		if (! $this->sparepartPictureTable) {
			$sm = $this->getServiceLocator ();
			$this->sparepartPictureTable = $sm->get ( 'Inventory\Model\SparepartPictureTable' );
		}
		return $this->sparepartPictureTable;
	}
	
}



