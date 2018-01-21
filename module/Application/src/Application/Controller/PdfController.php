<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Service\PdfService;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author nmt
 *        
 */
class PdfController extends AbstractActionController {
	protected $pdfService;
	protected $doctrineEM;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 * 
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function testAction() {		
		$details = 'If you can see this PDF file, the PDF service has been configurated successfully! :-)';
		
		$content = $this->pdfService->testPdf($details);
		
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/x-pdf' );
		$response->setContent ( $content );
		return $response;
	}
	
	// SETTER AND GETTER
	
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	public function getPdfService() {
		return $this->pdfService;
	}
	public function setPdfService(PdfService $pdfService) {
		$this->pdfService = $pdfService;
		return $this;
	}
	
}
