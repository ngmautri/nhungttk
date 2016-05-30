<?php
namespace Application\Service;

use Zend\Permissions\Acl\Acl;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Controller\ControllerManager;
use MLA\Service\AbstractService;
use ZendPdf\PdfDocument;

/*
 * @author nmt
 *
 */
class PdfService extends AbstractService
{	
	protected $moduleManager;
	protected $controllerManager;
	
	
	public function initAcl(Acl $acl){
		// TODO
	}
	
	public function createPdf(){
		// Create a new PDF document
		$pdf = new PdfDocument();		
		$pdf->save("test.pdf");
	}
	
	
	
}