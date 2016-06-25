<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\I18n\Validator\Int;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use MLA\Files;
use Application\Model\Department;
use Application\Model\DepartmentTable;
use Application\Model\DepartmentMember;
use Application\Model\DepartmentMemberTable;
use Application\Service\PdfService;

use Procurement\Model\PurchaseRequestTable;
use Procurement\Model\DeliveryItemTable;
/*
 * Control Panel Controller
 */
class PdfController extends AbstractActionController {
	
	Protected $pdfService;
	Protected $prTable;
	
	Protected $dnTable;
	Protected $dnItemTable;	
	
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		$content =  $this->pdfService->createPdf();
		$response = $this->getResponse();
		$response->getHeaders()->addHeaderLine( 'Content-Type', 'application/x-pdf' );
		$response->setContent($content);
		return $response;
	}
	
	public function prAction(){
		$pr_id = $this->params ()->fromQuery ( 'id' );
		$pr_items=$this->prTable->getSubmittedPR($pr_id);
		
		if(count($pr_items)>0){
			
			/*
			$requester = $pr_items->current()->pr_requester_name;
			$department = $pr_items->current()->pr_requester_name;
			$date = $pr_items->current()->pr_requester_name;
			$pr_number = $pr_items->current()->pr_requester_name;
			$pr_auto_number =$pr_items->current()->pr_requester_name;
			
			*/
			
		
			$details = '<div style="with=100%">
					<table  style="font-size:9pt;
    border-spacing: 0; padding:3px;
    border: 1px solid #cbcbcb;	width:100%;display: block;max-width: 100%;white-space: nowrap">';
			
			$details=$details.'<tr style="line-height: 15em;border: 1px solid #cbcbcb; background-color: #f2f2f2; font-weight: bold; text-align: center;">';
			
			$details=$details.'<td style="border: 1pt solid #cbcbcb;width:30px " > No.</td>';
			$details=$details.'<td style="border: 1pt solid #cbcbcb;width:50px " > Priority</td>';
			$details=$details.'<td style="border: 1px solid #cbcbcb;width:120px "> Name</td>';
			$details=$details.'<td style="border: 1px solid #cbcbcb;"> Code</td>';
			$details=$details.'<td style="border: 1px solid #cbcbcb;width:45px "> Unit</td>';
			$details=$details.'<td style="border: 1px solid #cbcbcb;width:45px; text-align: right;"> Q\'Ty</td>';
			$details=$details.'<td style="border: 1px solid #cbcbcb;"> Unit Price</td>';
			$details=$details.'<td style="border: 1px solid #cbcbcb; width:65px"> EDT</td>';
			$details=$details.'<td style="border: 1px solid #cbcbcb;width:auto"> Remarks</td>';
			$details=$details.'</tr>';
			
			
			$n=0;
			foreach ($pr_items as $item){
				$n=$n+1;
				
				if($n == 1){
					$requester = $item->pr_requester_name . ' (' . $item->email.')';
					$department = $item->pr_of_department;
					$date =  date_format(date_create($item->updated_on),"d-m-Y");
					$pr_number = $item->pr_number;
					$pr_auto_number =$item->auto_pr_number;
				}
				
				
				$details=$details.'<tr style="border: 1px solid #cbcbcb;line-height: 12em;vertical-align:middle" >';
			
				$details=$details.'<td style="border: 1px solid #cbcbcb;">'. $n.'</td>';
				$details=$details.'<td style="border: 1px solid #cbcbcb;">'. $item->priority.'</td>';
				$details=$details.'<td style="border: 1px solid #cbcbcb;">'. $item->name.'</td>';
					$details=$details.'<td style="border: 1px solid #cbcbcb;">'. $item->code.'</td>';
				$details=$details.'<td style="border: 1px solid #cbcbcb;text-align: center;">'. $item->unit.'</td>';
				$details=$details.'<td style="border: 1px solid #cbcbcb;text-align: right;">'. $item->quantity.'</td>';
				$details=$details.'<td style="border: 1px solid #cbcbcb;"> </td>';
				$details=$details.'<td style="border: 1px solid #cbcbcb;text-align: right;">'. date_format(date_create($item->EDT),"d-m-Y").'</td>';
					$details=$details.'<td style="border: 1px solid #cbcbcb;">'. $item->remarks.'</td>';
				
				
				$details=$details.'</tr>';
			}
			$details = $details.'</table></div>';
			
			$content = $this->pdfService->savePRAsPdf($requester, $department, $date, $pr_number, $pr_auto_number, $details);
			
			$response = $this->getResponse();
			$response->getHeaders()->addHeaderLine( 'Content-Type', 'application/x-pdf' );
			$response->setContent($content);
			return $response;
			
		}
		return null;
	}
	
	
	public function dnAction(){
		$dn_id = $this->params ()->fromQuery ( 'id' );
		$dn_items=$this->dnItemTable->getItemsByDN($dn_id);
	
		if(count($dn_items)>0){
				
			/*
				$requester = $pr_items->current()->pr_requester_name;
				$department = $pr_items->current()->pr_requester_name;
				$date = $pr_items->current()->pr_requester_name;
				$pr_number = $pr_items->current()->pr_requester_name;
				$pr_auto_number =$pr_items->current()->pr_requester_name;
					
				*/
				
	
			$details = '<div style="with=100%">
					<table  style="font-size:9pt;
    border-spacing: 0; padding:3px;
    border: 1px solid #cbcbcb;	width:100%;display: block;max-width: 100%;white-space: nowrap">';
				
			$details=$details.'<tr style="line-height: 15em;border: 1px solid #cbcbcb; background-color: #f2f2f2; font-weight: bold; text-align: center; vertical-align: middle">';
			$details=$details.'<td style="border: 1pt solid #cbcbcb;width:30px " > No.</td>';
			$details=$details.'<td style="border: 1pt solid #cbcbcb;" > PR.No.</td>';
			$details=$details.'<td style="border: 1px solid #cbcbcb;width:120px "> Item Name</td>';
			$details=$details.'<td style="border: 1px solid #cbcbcb;width:55px; text-align: right;"> Ordered <br/> Quantity</td>';
			$details=$details.'<td style="border: 1px solid #cbcbcb;width:60px; text-align: right;"> Delivered <br/> Quantity</td>';
			$details=$details.'<td style="border: 1px solid #cbcbcb;width:45px "> Unit</td>';
			$details=$details.'<td style="border: 1px solid #cbcbcb;"> Requester</td>';
			$details=$details.'<td style="border: 1px solid #cbcbcb;width:auto"> Signature</td>';
			$details=$details.'</tr>';
				
				
			$n=0;
			foreach ($dn_items as $item){
				$n=$n+1;
	
				$details=$details.'<tr style="border: 1px solid #cbcbcb;line-height: 12em;vertical-align:middle; adding:5pt" >';
					
				$details=$details.'<td style="border: 1px solid #cbcbcb;">'. $n.'</td>';
				$details=$details.'<td style="border: 1px solid #cbcbcb;">'. $item->pr_number.'</td>';
				$details=$details.'<td style="border: 1px solid #cbcbcb;">'. $item->name.'</td>';
				$details=$details.'<td style="border: 1px solid #cbcbcb;text-align: right;">'. $item->quantity.'</td>';
				$details=$details.'<td style="border: 1px solid #cbcbcb;text-align: right;">'. $item->delivered_quantity.'</td>';
				$details=$details.'<td style="border: 1px solid #cbcbcb;">'. $item->unit.'</td>';
				$details=$details.'<td style="border: 1px solid #cbcbcb;">'. $item->firstname .' ' . $item->lastname . '</td>';
				$details=$details.'<td style="border: 1px solid #cbcbcb;"></td>';
				$details=$details.'</tr>';
			}
			$details = $details.'</table></div>';
				
			$content = $this->pdfService->saveDNAsPdf($details);
				
			$response = $this->getResponse();
			$response->getHeaders()->addHeaderLine( 'Content-Type', 'application/x-pdf' );
			$response->setContent($content);
			return $response;
				
		}
		return null;
	}
	
	
	// SETTER AND GETTER
	
	public function getPdfService() {
		return $this->pdfService;
	}
	public function setPdfService(PdfService $pdfService) {
		$this->pdfService = $pdfService;
		return $this;
	}
	public function getPrTable() {
		return $this->prTable;
	}
	public function setPrTable(PurchaseRequestTable $prTable) {
		$this->prTable = $prTable;
		return $this;
	}
	public function getDnTable() {
		return $this->dnTable;
	}
	public function setDnTable($dnTable) {
		$this->dnTable = $dnTable;
		return $this;
	}
	public function getDnItemTable() {
		return $this->dnItemTable;
	}
	public function setDnItemTable(DeliveryItemTable $dnItemTable) {
		$this->dnItemTable = $dnItemTable;
		return $this;
	}
	
	
	
	
	

	

}
