<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace PM\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use PM\Service\ProjectSearchService;

/*
 * Control Panel Controller
 */
class ProjectSearchController extends AbstractActionController {
	protected $doctrineEM;
	protected $projectSearchService;
	
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
			$results = $this->projectSearchService->search( $q );
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
		
		// accepted only ajax request
		
		if (! $request->isXmlHttpRequest ()) {
			return $this->redirect ()->toRoute ( 'access_denied' );
		}
		
		$this->layout ( "layout/user/ajax" );
		
		$q = $this->params ()->fromQuery ( 'q' );
		$context = $this->params ()->fromQuery ( 'context' );
		$target_id= $this->params ()->fromQuery ( 'target_id' );
		$target_name= $this->params ()->fromQuery ( 'target_name' );
		
		$results = [ 
				"message" => "",
				"hits" => null,
				'context' => $context,
				'target_id' => $target_id,
				'target_name' => $target_name,
		];
		
		if ($q !== null) {
			if ($q !== "") {
				$results = $this->projectSearchService->search( $q );
			}
		}
		
		// var_dump($results);
		return new ViewModel ( array (
				'message' => $results ["message"],
				'hits' => $results ["hits"],
				'context' => $context,
				'target_id' => $target_id,
				'target_name' => $target_name,
				
		) );
	}
	
	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function createIndexAction() {
		$result = $this->projectSearchService->createIndex();
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


	public function getProjectSearchService() {
		return $this->projectSearchService;
	}
	public function setProjectSearchService($projectSearchService) {
		$this->projectSearchService = $projectSearchService;
		return $this;
	}
	
	
}
