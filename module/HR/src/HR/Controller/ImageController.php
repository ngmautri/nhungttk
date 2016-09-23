<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use HR\Model\Employee;
use HR\Model\EmployeeTable;
use HR\Model\EmployeePicture;
use HR\Model\EmployeePictureTable;

/**
 * 
 * @author nmt
 *
 */
class ImageController extends AbstractActionController {
	
	public $userTable;
	public $authService;

	protected $employeeTable;
	protected $employeePictureTable;
	protected $employeeService;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		
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
	

	/**
	 * 
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function articleAction() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
	
		$pic = $this->getArticlePictureTable()->get( $id);
	
		$response = $this->getResponse();
	
		$imageContent =  file_get_contents($pic->url);
		$response->setContent($imageContent);
		$response->getHeaders()
		->addHeaderLine('Content-Transfer-Encoding', 'binary')
		->addHeaderLine('Content-Type', 'image/jpeg')
		->addHeaderLine('Content-Length', mb_strlen($imageContent));
		return $response;
	}
	
	/**
	 * 
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function employeeThumbnail200Action() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$pic = $this->employeePictureTable->get($id);
		$response = $this->getResponse();
	
		$imageContent =  file_get_contents($pic->folder.DIRECTORY_SEPARATOR.'thumbnail_200_'.$pic->filename);
		$response->setContent($imageContent);
		$response->getHeaders()
		->addHeaderLine('Content-Transfer-Encoding', 'binary')
		->addHeaderLine('Content-Type', $pic->filetype)
		->addHeaderLine('Content-Length', mb_strlen($imageContent));
		return $response;
	}
	
	/**
	 * 
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function articleThumbnail200Action() {
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
	
		$pic = $this->getArticlePictureTable()->get( $id);
	
		$response = $this->getResponse();
	
		$imageContent =  file_get_contents($pic->folder.DIRECTORY_SEPARATOR.'thumbnail_200_'.$pic->filename);
		$response->setContent($imageContent);
		$response->getHeaders()
		->addHeaderLine('Content-Transfer-Encoding', 'binary')
		->addHeaderLine('Content-Type', 'image/jpeg')
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
	
	public function articleThumbnail150Action() {
	
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$pic = $this->getArticlePictureTable()->get( $id);
		$response = $this->getResponse();
		$imageContent =  file_get_contents($pic->folder.DIRECTORY_SEPARATOR.'thumbnail_150_'.$pic->filename);
		$response->setContent($imageContent);
		$response->getHeaders()
		->addHeaderLine('Content-Transfer-Encoding', 'binary')
		->addHeaderLine('Content-Type', 'image/jpeg')
		->addHeaderLine('Content-Length', mb_strlen($imageContent));
	
		return $response;
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function deleteSparePartPictureAction() {
		
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
		
	
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
			
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
			$this->redirect()->toUrl($redirectUrl);
		}
	
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$pic= $this->getSparepartPictureTable ()->get ( $id );
		
		
	
		return new ViewModel ( array (
				'picture' => $pic,
				'redirectUrl'=>$redirectUrl,
				
		) );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function deleteArticlePictureAction() {
	
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
		if ($request->isPost ()) {
			$del = $request->getPost ( 'delete_confirmation', 'NO' );
	
			if ($del === 'YES') {
	
				$id = ( int ) $request->getPost ( 'id' );
				$pic= $this->getArticlePictureTable ()->get ( $id );
				$filename = $pic->url;
	
				if (file_exists($filename)) {
					unlink($filename);
				}
				$this->getArticlePictureTable ()->delete ($id);
	
			}
				
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
			$this->redirect()->toUrl($redirectUrl);
		}
	
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$pic= $this->getArticlePictureTable ()->get ( $id );
		
		return new ViewModel ( array (
				'picture' => $pic,
				'redirectUrl'=>$redirectUrl,
	
		) );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function deleteAssetPictureAction() {
	
		$request = $this->getRequest ();
		$redirectUrl = $this->getRequest()->getHeader('Referer')->getUri();
		
	
		if ($request->isPost ()) {
			$del = $request->getPost ( 'delete_confirmation', 'NO' );
	
			if ($del === 'YES') {
	
				$id = ( int ) $request->getPost ( 'id' );
				$pic= $this->getAssetPictureTable ()->get ( $id );
				$filename = $pic->url;
	
				if (file_exists($filename)) {
					unlink($filename);
				}
				$this->getAssetPictureTable ()->delete ($id);
	
			}
			$redirectUrl  = $request->getPost ( 'redirectUrl' );
			$this->redirect()->toUrl($redirectUrl);
		}
	
		$id = ( int ) $this->params ()->fromQuery ( 'id' );
		$pic= $this->getAssetPictureTable ()->get ( $id );
	
	
	
		return new ViewModel ( array (
				'picture' => $pic,
				'redirectUrl'=>$redirectUrl,
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
	
	private function getArticlePictureTable() {
		if (! $this->articlePictureTable) {
			$sm = $this->getServiceLocator ();
			$this->articlePictureTable = $sm->get ( 'Inventory\Model\ArticlePictureTable' );
		}
		return $this->articlePictureTable;
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
	public function getEmployeePictureTable() {
		return $this->employeePictureTable;
	}
	public function setEmployeePictureTable(EmployeePictureTable $employeePictureTable) {
		$this->employeePictureTable = $employeePictureTable;
		return $this;
	}
	public function getEmployeeTable() {
		return $this->employeeTable;
	}
	public function setEmployeeTable($employeeTable) {
		$this->employeeTable = $employeeTable;
		return $this;
	}
	public function getEmployeeService() {
		return $this->employeeService;
	}
	public function setEmployeeService($employeeService) {
		$this->employeeService = $employeeService;
		return $this;
	}
	
	
	
}



