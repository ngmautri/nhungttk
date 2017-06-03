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
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;

class ItemPictureController extends AbstractActionController {
	protected $doctrineEM;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 * 
	 * @return void|\Zend\Stdlib\ResponseInterface
	 */
	public function getAction() {
		
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
	public function thumbnail200Action() {
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
	
	/**
	 * Return attachment of a target
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function list1Action() {
		$request = $this->getRequest ();
		
		// accepted only ajax request
		if (! $request->isXmlHttpRequest ()) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		;
		
		$this->layout ( "layout/user/ajax" );
		
		$target_id = ( int ) $this->params ()->fromQuery ( 'target_id' );
		$checksum = $this->params ()->fromQuery ( 'checksum' );
		$token = $this->params ()->fromQuery ( 'token' );
		$criteria = array (
				'id' => $target_id,
				'checksum' => $checksum,
				'token' => $token
		);
		
		/**
		 *
		 * @todo : Change Target
		 */
		$target = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findOneBy ( $criteria );
		
		if ($target !== null) {
			
			/**
			 *
			 * @todo : Change Target
			 */
			$criteria = array (
					'item' => $target_id,
			);
			
			$list = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItemPicture' )->findBy ( $criteria );
			$total_records = count ( $list );
			$paginator = null;
			
		/* 	$this->getResponse()->getHeaders ()->addHeaderLine('Expires', '3800', true);
			$this->getResponse()->getHeaders ()->addHeaderLine('Cache-Control', 'public', true);
			$this->getResponse()->getHeaders ()->addHeaderLine('Cache-Control', 'max-age=3800');
			$this->getResponse()->getHeaders ()->addHeaderLine('Pragma', '', true);
		 */	
			
			return new ViewModel ( array (
					'list' => $list,
					'total_records' => $total_records,
					'paginator' => $paginator,
					'target' => $target
			) );
		} else {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
	}
	
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
}



