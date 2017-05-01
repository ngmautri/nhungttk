<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace BP\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use BP\Service\VendorSearchService;

/*
 * Control Panel Controller
 */
class VendorSearchController extends AbstractActionController {
	protected $doctrineEM;
	protected $vendorSearchService;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		return new ViewModel ( array () );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function doAction() {
		$q = $this->params ()->fromQuery ( 'q' );
		
		if ($q !== "") {
			$results = $this->vendorSearchService->search ( $q );
		} else {
			$results = [ 
					"message" => "",
					"hits" => null 
			];
		}
		
		// var_dump($results);
		return new ViewModel ( array (
				'message' => $results ["message"],
				'hits' => $results ["hits"] 
		) );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function do1Action() {
		$request = $this->getRequest ();
		$context = $this->params ()->fromQuery ( 'context' );
		
		
		// accepted only ajax request
		if (! $request->isXmlHttpRequest ()) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		$this->layout ( "layout/user/ajax" );
		
		$q = $this->params ()->fromQuery ( 'q' );
		
		if ($q !== "") {
			$results = $this->vendorSearchService->search ( $q );
		} else {
			$results = [
					"message" => "",
					"hits" => null,
					'context' =>$context,
			];
		}
		
		// var_dump($results);
		return new ViewModel ( array (
				'message' => $results ["message"],
				'hits' => $results ["hits"],
				'context' =>$context,
		) );
	}
	
	/**
	 */
	public function createIndexAction() {
		$result = $this->vendorSearchService->createVendorIndex ();
		return new ViewModel ( array (
				'result' => $result 
		) );
	}
	
	/**
	 *
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	
	/**
	 *
	 * @param EntityManager $doctrineEM        	
	 * @return \BP\Controller\VendorController
	 */
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	public function getVendorSearchService() {
		return $this->vendorSearchService;
	}
	public function setVendorSearchService(VendorSearchService $vendorSearchService) {
		$this->vendorSearchService = $vendorSearchService;
		return $this;
	}
	
}
