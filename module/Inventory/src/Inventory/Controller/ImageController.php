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
use Inventory\Model\ArticlePictureTable;
use Inventory\Model\AssetPictureTable;
use Inventory\Model\SparepartPictureTable;
use Doctrine\ORM\EntityManager;

class ImageController extends AbstractActionController {
	public $userTable;
	public $authService;
	public $massage = 'NULL';
	public $assetPictureTable;
	public $sparepartPictureTable;
	public $articlePictureTable;
	protected $doctrineEM;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		
		$pic = $this->getAssetPictureTable ()->get ( $id );
		
		$response = $this->getResponse ();
		
		$imageContent = file_get_contents ( $pic->url );
		$response->setContent ( $imageContent );
		$response->getHeaders ()->addHeaderLine ( 'Content-Transfer-Encoding', 'binary' )->addHeaderLine ( 'Content-Type', 'image/jpeg' )->addHeaderLine ( 'Content-Length', mb_strlen ( $imageContent ) );
		
		return $response;
	}
	
	/*
	 * Defaul Action
	 */
	public function itemAction() {
		$entity_id = ( int ) $this->params ()->fromQuery ( 'entity_id' );
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		$token = $this->params ()->fromQuery ( 'token' );
		$criteria = array (
				'id' => $entity_id,
				'checksum' => $checksum,
				'token' => $token 
		);
		
		$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPicture' )->findOneBy ( $criteria );
		
		if ($entity !== null) {
			
			$pic = new \Application\Entity\NmtInventoryItemPicture ();
			$pic = $entity;
			$pic_folder = getcwd () . "/data/inventory/picture/item/" . $pic->getFolderRelative () . $pic->getFileName ();
			$imageContent = file_get_contents ( $pic_folder );
			
			$response = $this->getResponse ();
			
			$response->setContent ( $imageContent );
			$response->getHeaders ()->addHeaderLine ( 'Content-Transfer-Encoding', 'binary' )->addHeaderLine ( 'Content-Type', $pic->getFiletype () )->addHeaderLine ( 'Content-Length', mb_strlen ( $imageContent ) );
			return $response;
		} else {
			return;
		}
	}
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function itemThumbnail200Action() {
		$entity_id = ( int ) $this->params ()->fromQuery ( 'entity_id' );
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		$token = $this->params ()->fromQuery ( 'token' );
		$criteria = array (
				'id' => $entity_id,
				'checksum' => $checksum,
				'token' => $token 
		);
		
		$entity = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPicture' )->findOneBy ( $criteria );
		
		if ($entity !== null) {
			
			$pic = new \Application\Entity\NmtInventoryItemPicture ();
			$pic = $entity;
			$pic_folder = getcwd () . "/data/inventory/picture/item/" . $pic->getFolderRelative () . "thumbnail_200_" . $pic->getFileName ();
			$imageContent = file_get_contents ( $pic_folder );
			
			$response = $this->getResponse ();
			
			$response->setContent ( $imageContent );
			$response->getHeaders ()->addHeaderLine ( 'Content-Transfer-Encoding', 'binary' )->addHeaderLine ( 'Content-Type', $pic->getFiletype () )->addHeaderLine ( 'Content-Length', mb_strlen ( $imageContent ) );
			return $response;
		} else {
			return;
		}
	}
	
	/*
	 * Defaul Action
	 */
	public function sparepartAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		
		$pic = $this->getSparepartPictureTable ()->get ( $id );
		
		$response = $this->getResponse ();
		
		$imageContent = file_get_contents ( $pic->url );
		
		$response->setContent ( $imageContent );
		$response->getHeaders ()->addHeaderLine ( 'Content-Transfer-Encoding', 'binary' )->addHeaderLine ( 'Content-Type', 'image/jpeg' )->addHeaderLine ( 'Content-Length', mb_strlen ( $imageContent ) );
		return $response;
	}
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function articleAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		
		$pic = $this->getArticlePictureTable ()->get ( $id );
		
		$response = $this->getResponse ();
		
		$imageContent = file_get_contents ( $pic->url );
		$response->setContent ( $imageContent );
		$response->getHeaders ()->addHeaderLine ( 'Content-Transfer-Encoding', 'binary' )->addHeaderLine ( 'Content-Type', 'image/jpeg' )->addHeaderLine ( 'Content-Length', mb_strlen ( $imageContent ) );
		return $response;
	}
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function assetThumbnail200Action() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$pic = $this->getAssetPictureTable ()->get ( $id );
		$response = $this->getResponse ();
		$imageContent = file_get_contents ( $pic->folder . DIRECTORY_SEPARATOR . 'thumbnail_200_' . $pic->filename );
		$response->setContent ( $imageContent );
		$response->getHeaders ()->addHeaderLine ( 'Content-Transfer-Encoding', 'binary' )->addHeaderLine ( 'Content-Type', $pic->filetype )->addHeaderLine ( 'Content-Length', mb_strlen ( $imageContent ) );
		
		return $response;
	}
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function sparepartThumbnail200Action() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		
		$pic = $this->getSparepartPictureTable ()->get ( $id );
		
		$response = $this->getResponse ();
		
		/*
		 * // Create Image From Existing File
		 * $jpg_image = imagecreatefromjpeg($pic->folder.DIRECTORY_SEPARATOR.'thumbnail_200_'.$pic->filename);
		 *
		 * // Allocate A Color For The Text
		 * $white = imagecolorallocate($jpg_image, 255, 255, 255);
		 *
		 * // Set Path to Font File
		 * $font_path = 'arial.ttf';
		 *
		 * // Set Text to Be Printed On Image
		 * $text = "This is a sunset!";
		 *
		 * // Print Text On Image
		 * imagettftext($jpg_image, 25, 0, 75, 300, $white, $font_path, $text);
		 *
		 * // Send Image to Browser
		 * imagejpeg($jpg_image);
		 *
		 *
		 * //$imageContent = file_get_contents($jpg_image);
		 *
		 */
		
		$imageContent = file_get_contents ( $pic->folder . DIRECTORY_SEPARATOR . 'thumbnail_200_' . $pic->filename );
		
		$response->setContent ( $imageContent );
		$response->getHeaders ()->addHeaderLine ( 'Content-Transfer-Encoding', 'binary' )->addHeaderLine ( 'Content-Type', 'image/png' )->addHeaderLine ( 'Content-Length', mb_strlen ( $imageContent ) );
		
		// Clear Memory
		// imagedestroy($jpg_image);
		
		return $response;
	}
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function articleThumbnail200Action() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		
		$pic = $this->getArticlePictureTable ()->get ( $id );
		
		$response = $this->getResponse ();
		
		$imageContent = file_get_contents ( $pic->folder . DIRECTORY_SEPARATOR . 'thumbnail_200_' . $pic->filename );
		$response->setContent ( $imageContent );
		$response->getHeaders ()->addHeaderLine ( 'Content-Transfer-Encoding', 'binary' )->addHeaderLine ( 'Content-Type', 'image/jpeg' )->addHeaderLine ( 'Content-Length', mb_strlen ( $imageContent ) );
		return $response;
	}
	public function assetThumbnail150Action() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$pic = $this->getAssetPictureTable ()->get ( $id );
		$response = $this->getResponse ();
		$imageContent = file_get_contents ( $pic->folder . DIRECTORY_SEPARATOR . 'thumbnail_150_' . $pic->filename );
		$response->setContent ( $imageContent );
		$response->getHeaders ()->addHeaderLine ( 'Content-Transfer-Encoding', 'binary' )->addHeaderLine ( 'Content-Type', 'image/jpeg' )->addHeaderLine ( 'Content-Length', mb_strlen ( $imageContent ) );
		
		return $response;
	}
	public function articleThumbnail150Action() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$pic = $this->getArticlePictureTable ()->get ( $id );
		$response = $this->getResponse ();
		$imageContent = file_get_contents ( $pic->folder . DIRECTORY_SEPARATOR . 'thumbnail_150_' . $pic->filename );
		$response->setContent ( $imageContent );
		$response->getHeaders ()->addHeaderLine ( 'Content-Transfer-Encoding', 'binary' )->addHeaderLine ( 'Content-Type', 'image/jpeg' )->addHeaderLine ( 'Content-Length', mb_strlen ( $imageContent ) );
		
		return $response;
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function deleteSparePartPictureAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		if ($request->isPost ()) {
			$del = $request->getPost ( 'delete_confirmation', 'NO' );
			
			if ($del === 'YES') {
				
				$id = ( int ) $request->getPost ( 'id' );
				$pic = $this->getSparepartPictureTable ()->get ( $id );
				$filename = $pic->url;
				
				if (file_exists ( $filename )) {
					unlink ( $filename );
				}
				$this->getSparepartPictureTable ()->delete ( $id );
			}
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$this->redirect ()->toUrl ( $redirectUrl );
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$pic = $this->getSparepartPictureTable ()->get ( $id );
		
		return new ViewModel ( array (
				'picture' => $pic,
				'redirectUrl' => $redirectUrl 
		
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function deleteArticlePictureAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		if ($request->isPost ()) {
			$del = $request->getPost ( 'delete_confirmation', 'NO' );
			
			if ($del === 'YES') {
				
				$id = ( int ) $request->getPost ( 'id' );
				$pic = $this->getArticlePictureTable ()->get ( $id );
				$filename = $pic->url;
				
				if (file_exists ( $filename )) {
					unlink ( $filename );
				}
				$this->getArticlePictureTable ()->delete ( $id );
			}
			
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$this->redirect ()->toUrl ( $redirectUrl );
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$pic = $this->getArticlePictureTable ()->get ( $id );
		
		return new ViewModel ( array (
				'picture' => $pic,
				'redirectUrl' => $redirectUrl 
		
		) );
	}
	
	/**
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function deleteAssetPictureAction() {
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest ()->getHeader ( 'Referer' )->getUri ();
		
		if ($request->isPost ()) {
			$del = $request->getPost ( 'delete_confirmation', 'NO' );
			
			if ($del === 'YES') {
				
				$id = ( int ) $request->getPost ( 'id' );
				$pic = $this->getAssetPictureTable ()->get ( $id );
				$filename = $pic->url;
				
				if (file_exists ( $filename )) {
					unlink ( $filename );
				}
				$this->getAssetPictureTable ()->delete ( $id );
			}
			$redirectUrl = $request->getPost ( 'redirectUrl' );
			$this->redirect ()->toUrl ( $redirectUrl );
		}
		
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$pic = $this->getAssetPictureTable ()->get ( $id );
		
		return new ViewModel ( array (
				'picture' => $pic,
				'redirectUrl' => $redirectUrl 
		) );
	}
	public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable($userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getAuthService() {
		return $this->authService;
	}
	public function setAuthService($authService) {
		$this->authService = $authService;
		return $this;
	}
	public function getAssetPictureTable() {
		return $this->assetPictureTable;
	}
	public function setAssetPictureTable(AssetPictureTable $assetPictureTable) {
		$this->assetPictureTable = $assetPictureTable;
		return $this;
	}
	public function getSparepartPictureTable() {
		return $this->sparepartPictureTable;
	}
	public function setSparepartPictureTable(SparepartPictureTable $sparepartPictureTable) {
		$this->sparepartPictureTable = $sparepartPictureTable;
		return $this;
	}
	public function getArticlePictureTable() {
		return $this->articlePictureTable;
	}
	public function setArticlePictureTable(ArticlePictureTable $articlePictureTable) {
		$this->articlePictureTable = $articlePictureTable;
		return $this;
	}
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
}



