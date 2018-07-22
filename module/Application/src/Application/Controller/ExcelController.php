<?php

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Service\ApplicationService;
use Application\Service\ExcelService;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author nmt
 *        
 */
class ExcelController extends AbstractActionController {
	protected $authService;
	protected $appService;
	protected $userTable;
	protected $excelService;
	protected $doctrineEM;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface|NULL
	 */
	public function testAction() {		
		$details = 'If you can see this PDF file, the PDF service has been configurated successfully! :-)';
		
		$content = $this->excelService->test();
		
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/x-pdf' );
		$response->setContent ( $content );
		return $response;
	}
	
	// SETTER AND GETTER
	public function getAuthService() {
		return $this->authService;
	}
	public function setAuthService($authService) {
		$this->authService = $authService;
		return $this;
	}
	public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable($userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getAppService() {
		return $this->appService;
	}
	public function setAppService(ApplicationService $appService) {
		$this->appService = $appService;
		return $this;
	}
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	public function getExcelService() {
		return $this->excelService;
	}
	public function setExcelService(ExcelService $excelService) {
		$this->excelService = $excelService;
		return $this;
	}
	

}
