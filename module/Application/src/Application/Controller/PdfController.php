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
use Procurement\Model\PurchaseRequestItemTableTable;
use Procurement\Model\DeliveryItemTable;
use Procurement\Model\PurchaseRequestItemTable;
use Inventory\Model\AssetCountingItemTable;
use Inventory\Model\AssetPictureTable;

/*
 * Control Panel Controller
 */
class PdfController extends AbstractActionController {
	protected $pdfService;
	protected $prTable;
	protected $prItemTable;
	protected $dnTable;
	protected $dnItemTable;
	protected $assetCountingItemTable;
	protected $assetPictureTable;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		$content = $this->pdfService->createPdf ();
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/x-pdf' );
		$response->setContent ( $content );
		return $response;
	}
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface|NULL
	 */
	public function assetCountingAction() {
		$id = $this->params ()->fromQuery ( 'id' );
		$category_id = $this->params ()->fromQuery ( 'category_id' );
		
		set_time_limit ( 200 );
		
		$counting_items = $this->assetCountingItemTable->getCountedItems ( $id, $category_id );
		
		if (count ( $counting_items ) > 0) {
			
			/*
			 * $requester = $pr_items->current()->pr_requester_name;
			 * $department = $pr_items->current()->pr_requester_name;
			 * $date = $pr_items->current()->pr_requester_name;
			 * $pr_number = $pr_items->current()->pr_requester_name;
			 * $pr_auto_number =$pr_items->current()->pr_requester_name;
			 *
			 */
			// $details='<img src="/images/bg1.png"/>';
			
			$details = '';
			
			$details = $details . '<div style="with=100%">
					<table  style="font-size:9pt;
    border-spacing: 0; padding:3px;
    border: 1px solid #cbcbcb;	width:100%;display: block;max-width: 100%;white-space: nowrap">';
			
			$details = $details . '<tr style="line-height: 12em;border: 1px solid #cbcbcb; background-color: #f2f2f2; font-weight: bold; text-align: center;">';
			$details = $details . '<td style="border: 1pt solid #cbcbcb;width:30px " > No.</td>';
			$details = $details . '<td style="border: 1pt solid #cbcbcb;width:70px " > Picture</td>';
			$details = $details . '<td style="border: 1pt solid #cbcbcb;width:200px " > Asset Name</td>';
			$details = $details . '<td style="border: 1pt solid #cbcbcb;width:140px " > Counted by <br> /Counted on</td>';
			$details = $details . '<td style="border: 1pt solid #cbcbcb;width:auto " > Remarks</td>';
			$details = $details . '</tr>';
			$n = 0;
			foreach ( $counting_items as $item ) {
				$n = $n + 1;
				$pic_file = "";
				
				if ($item->asset_picture_id != null) {
					$pic = $this->assetPictureTable->get ( $item->asset_picture_id );
					
					if ($pic != null) {
						if (file_exists ( $pic->url )) {
							$pic_file = $pic->url;
						}
					}
				}
				$details = $details . '<tr style="border: 1px solid #cbcbcb;line-height: 13em;vertical-align:middle" >';
				$details = $details . '<td style="border: 1px solid #cbcbcb;">' . $n . '</td>';
				if ($pic_file != "") {
					// $details = $details . '<td style="border: 1px solid #cbcbcb;"><img width="50" height="50" src="' . $pic_file . '"></td>';
					$details = $details . '<td style="border: 1px solid #cbcbcb;">-</td>';
				} else {
					$details = $details . '<td style="border: 1px solid #cbcbcb;">-</td>';
				}
				
				$details = $details . '<td style="border: 1px solid #cbcbcb;"><b>' . $item->name . '</br><br>Tag:' . $item->tag . '<br>Model:' . $item->model . '<br>Serial:' . $item->serial . '<br>Location:' . $item->counted_location . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;">' . $item->counted_by . '<br>/' . $item->counted_on . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;"></td>';
				$details = $details . '</tr>';
			}
			$details = $details . '</table></div>';
			
			$content = $this->pdfService->saveAssetCountingPdf ( $details );
			
			$response = $this->getResponse ();
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/x-pdf' );
			$response->setContent ( $content );
			return $response;
		}
		
		return null;
	}
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface|NULL
	 */
	public function prAction() {
		$pr_id = $this->params ()->fromQuery ( 'id' );
		// $pr_items=$this->prTable->getSubmittedPR($pr_id);
		
		$pr_items = $this->prItemTable->getItemsByPR3 ( $pr_id );
		
		if (count ( $pr_items ) > 0) {
			
			/*
			 * $requester = $pr_items->current()->pr_requester_name;
			 * $department = $pr_items->current()->pr_requester_name;
			 * $date = $pr_items->current()->pr_requester_name;
			 * $pr_number = $pr_items->current()->pr_requester_name;
			 * $pr_auto_number =$pr_items->current()->pr_requester_name;
			 *
			 */
			// $details='<img src="/images/bg1.png"/>';
			$details = '';
			$details = $details . '<div style="with=100%">
					<table  style="font-size:9pt;
    border-spacing: 0; padding:3px;
    border: 1px solid #cbcbcb;	width:100%;display: block;max-width: 100%;white-space: nowrap">';
			
			$details = $details . '<tr style="line-height: 12em;border: 1px solid #cbcbcb; background-color: #f2f2f2; font-weight: bold; text-align: center;">';
			
			$details = $details . '<td style="border: 1pt solid #cbcbcb;width:30px " > No.</td>';
			$details = $details . '<td style="border: 1pt solid #cbcbcb;width:55px " >EDT<br> /Priority</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;width:200px "> Item Name / Code</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;width:45px "> Unit</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;width:45px; text-align: right;"> Q\'Ty</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;"> Unit Price</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;"> Total Price</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;width:auto"> Remarks</td>';
			$details = $details . '</tr>';
			
			$n = 0;
			foreach ( $pr_items as $item ) {
				$n = $n + 1;
				
				if ($n == 1) {
					$requester = $item->pr_requester_name . ' (' . $item->requester_email . ')';
					$department = $item->pr_of_department;
					$date = date_format ( date_create ( $item->created_on ), "d-m-Y" );
					$pr_number = $item->pr_number;
					$pr_auto_number = $item->pr_auto_number;
				}
				
				$details = $details . '<tr style="border: 1px solid #cbcbcb;line-height: 13em;vertical-align:middle" >';
				
				$details = $details . '<td style="border: 1px solid #cbcbcb;">' . $n . '</td>';
				
				if($item->priority =="Urgent"){
					$details = $details . '<td style="border: 1px solid #cbcbcb; vertical-align: middle; line-height: 13em;">' . date_format ( date_create ( $item->EDT ), "d-m-y" ) . '<div style="color: red; font-style: italic; font-size: 8pt;">' . $item->priority . '</div></td>';
				}else{
					$details = $details . '<td style="border: 1px solid #cbcbcb; vertical-align: middle; line-height: 13em;">' . date_format ( date_create ( $item->EDT ), "d-m-y" ) . '<div style="color: gray; font-style: italic; font-size: 8pt;">' . $item->priority . '</div></td>';
				}
				
				$more = '<div style="padding-top: 50px;font-style: italic; font-size: 9pt;">';
				
				$d = '';
				if ($item->sp_tag > 0) {
					$d = $d . '- Tag: ' . $item->sp_tag;
				}
				
				if ($item->code != null) {
					if ($d == '') {
						$d = $d . '- Code: ' . $item->code;
					} else {
						$d = $d . '<br>- Code: ' . $item->code;
					}
				}
				
				if ($item->asset_name != null) {
					if ($d == '') {
						$d = $d . '- Model: ' . $item->asset_name;
					} else {
						$d = $d . '<br>- Model: ' . $item->asset_name;
					}
				}
				
				$more =$more. $d . '</div>';
				
				$details = $details . '<td style="border: 1px solid #cbcbcb;line-height: 15em;"><b>' . ucwords ( $item->name ) . '</b>' . $more . '</td>';
				
				$details = $details . '<td style="border: 1px solid #cbcbcb;text-align: center;">' . $item->unit . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;text-align: right;">' . $item->quantity . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;"> </td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;"> </td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;">' . $item->remarks . '</td>';
				
				$details = $details . '</tr>';
			}
			$details = $details . '</table></div>';
			
			$content = $this->pdfService->savePRAsPdf ( $requester, $department, $date, $pr_number, $pr_auto_number, $details );
			
			$response = $this->getResponse ();
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/x-pdf' );
			$response->setContent ( $content );
			return $response;
		}
		return null;
	}
	
	/**
	 *
	 * @return \Zend\Stdlib\ResponseInterface|NULL
	 */
	public function poAction() {
		$pr_id = $this->params ()->fromQuery ( 'pr_id' );
		// $pr_items=$this->prTable->getSubmittedPR($pr_id);
		
		$pr_items = $this->prItemTable->getItemsByPR3 ( $pr_id );
		$po_grand_total = $this->prItemTable->getPOGrandTotal ( $pr_id );
		
		if (count ( $pr_items ) > 0) {
			
			/*
			 * $requester = $pr_items->current()->pr_requester_name;
			 * $department = $pr_items->current()->pr_requester_name;
			 * $date = $pr_items->current()->pr_requester_name;
			 * $pr_number = $pr_items->current()->pr_requester_name;
			 * $pr_auto_number =$pr_items->current()->pr_requester_name;
			 *
			 */
			
			$details = '<div style="with=100%">
					<table  style="font-size:9pt;
    border-spacing: 0; padding:3px;
    border: 1px solid #cbcbcb;	width:100%;display: block;max-width: 100%;white-space: nowrap">';
			
			$details = $details . '<tr style="line-height: 12em;border: 1px solid #cbcbcb; background-color: #f2f2f2; font-weight: bold; text-align: center;">';
			
			$details = $details . '<td style="border: 1pt solid #cbcbcb;width:30px " > No.</td>';
			$details = $details . '<td style="border: 1pt solid #cbcbcb;width:55px " >EDT<br> /Priority</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;width:180px "> Item Name <br> /Item Code</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;width:45px "> Unit</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;width:45px; text-align: right;"> Q\'Ty</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;"> Unit <br> Price</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;"> Total <br>Price</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb; width:38px"> Curr.</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;width:auto"> Remarks</td>';
			$details = $details . '</tr>';
			
			$n = 0;
			foreach ( $pr_items as $item ) {
				$n = $n + 1;
				
				if ($n == 1) {
					$requester = $item->pr_requester_name . ' (' . $item->requester_email . ')';
					$department = $item->pr_of_department;
					$date = date_format ( date_create ( $item->created_on ), "d-m-Y" );
					$pr_number = $item->pr_number;
					$pr_auto_number = $item->pr_auto_number;
				}
				
				$details = $details . '<tr style="border: 1px solid #cbcbcb;line-height: 13em;vertical-align:middle" >';
				
				$details = $details . '<td style="border: 1px solid #cbcbcb;">' . $n . '</td>';
				
				if($item->priority =="Urgent"){
					$details = $details . '<td style="border: 1px solid #cbcbcb; vertical-align: middle; line-height: 13em;">' . date_format ( date_create ( $item->EDT ), "d-m-y" ) . '<div style="color: red; font-style: italic; font-size: 8pt;">' . $item->priority . '</div></td>';
				}else{
					$details = $details . '<td style="border: 1px solid #cbcbcb; vertical-align: middle; line-height: 13em;">' . date_format ( date_create ( $item->EDT ), "d-m-y" ) . '<div style="color: gray; font-style: italic; font-size: 8pt;">' . $item->priority . '</div></td>';
				}
				
				$more = '<div style="padding-top: 50px;font-style: italic; font-size: 8pt;">';
				
				$d = '';
				if ($item->sp_tag > 0) {
					$d = $d . '- Tag: ' . $item->sp_tag;
				}
				
				if ($item->code != null) {
					if ($d == '') {
						$d = $d . '- Code: ' . $item->code;
					} else {
						$d = $d . '<br>- Code: ' . $item->code;
					}
				}
				
				if ($item->asset_name != null) {
					if ($d == '') {
						$d = $d . '- Model: ' . $item->asset_name;
					} else {
						$d = $d . '<br>- Model: ' . $item->asset_name;
					}
				}
				
				$more =$more. $d . '</div>';
				
				$details = $details . '<td style="border: 1px solid #cbcbcb;line-height: 15em;"><b>' . ucwords ( $item->name ) . '</b>' . $more . '</td>';
				
				$details = $details . '<td style="border: 1px solid #cbcbcb;text-align: center;">' . $item->unit . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;text-align: right;">' . $item->quantity . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;text-align: right;">' . number_format ( $item->po_price, 0, ",", "." ) . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;text-align: right;">' . number_format ( $item->po_price * $item->quantity, 0, ",", "." ) . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;text-align: right;">' . $item->po_currency . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;font-size: 8pt;">' . $item->po_vendor_name . ' - ' . $item->po_payment_method . '<br>' . $item->po_remarks . '</td>';
				
				$details = $details . '</tr>';
			}
			if (count ( $po_grand_total ) > 0) {
				
				foreach ( $po_grand_total as $a ) {
					$details = $details . '<tr><td colspan="6" style="border: 1px solid #cbcbcb;text-align: right;"><b>Grand Total:</b></td>';
					$details = $details . '<td style="border: 1px solid #cbcbcb;text-align: right;font-weight: bold;">' . number_format ( $a->grand_total, 0, ",", "." ) . '</td>';
					$details = $details . '<td style="border: 1px solid #cbcbcb;text-align: right; font-weight: bold;">' . $a->currency . '</td></tr>';
				}
			}
			
			$details = $details . '</table></div>';
			
			$content = $this->pdfService->savePOAsPdf ( $requester, $department, $date, $pr_number, $pr_auto_number, $details );
			
			$response = $this->getResponse ();
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/x-pdf' );
			$response->setContent ( $content );
			return $response;
		}
		return null;
	}
	public function dnAction() {
		$dn_id = $this->params ()->fromQuery ( 'id' );
		$dn_items = $this->dnItemTable->getItemsByDN ( $dn_id );
		
		if (count ( $dn_items ) > 0) {
			
			/*
			 * $requester = $pr_items->current()->pr_requester_name;
			 * $department = $pr_items->current()->pr_requester_name;
			 * $date = $pr_items->current()->pr_requester_name;
			 * $pr_number = $pr_items->current()->pr_requester_name;
			 * $pr_auto_number =$pr_items->current()->pr_requester_name;
			 *
			 */
			
			$details = '<div style="with=100%">
					<table  style="font-size:9pt;
    border-spacing: 0; padding:3px;
    border: 1px solid #cbcbcb;	width:100%;display: block;max-width: 100%;white-space: nowrap">';
			
			$details = $details . '<tr style="line-height: 15em;border: 1px solid #cbcbcb; background-color: #f2f2f2; font-weight: bold; text-align: center; vertical-align: middle">';
			$details = $details . '<td style="border: 1pt solid #cbcbcb;width:30px " > No.</td>';
			$details = $details . '<td style="border: 1pt solid #cbcbcb;" > PR.No.</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;width:120px "> Item Name</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;width:55px; text-align: right;"> Ordered <br/> Quantity</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;width:60px; text-align: right;"> Delivered <br/> Quantity</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;width:45px "> Unit</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;"> Requester</td>';
			$details = $details . '<td style="border: 1px solid #cbcbcb;width:auto"> Signature</td>';
			$details = $details . '</tr>';
			
			$n = 0;
			foreach ( $dn_items as $item ) {
				$n = $n + 1;
				
				$details = $details . '<tr style="border: 1px solid #cbcbcb;line-height: 12em;vertical-align:middle; adding:5pt" >';
				
				$details = $details . '<td style="border: 1px solid #cbcbcb;">' . $n . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;">' . $item->pr_number . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;">' . $item->name . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;text-align: right;">' . $item->quantity . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;text-align: right;">' . $item->delivered_quantity . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;">' . $item->unit . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;">' . $item->firstname . ' ' . $item->lastname . '</td>';
				$details = $details . '<td style="border: 1px solid #cbcbcb;"></td>';
				$details = $details . '</tr>';
			}
			$details = $details . '</table></div>';
			
			$content = $this->pdfService->saveDNAsPdf ( $details );
			
			$response = $this->getResponse ();
			$response->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/x-pdf' );
			$response->setContent ( $content );
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
	public function getPrItemTable() {
		return $this->prItemTable;
	}
	public function setPrItemTable(PurchaseRequestItemTable $prItemTable) {
		$this->prItemTable = $prItemTable;
		return $this;
	}
	public function getAssetCountingItemTable() {
		return $this->assetCountingItemTable;
	}
	public function setAssetCountingItemTable(AssetCountingItemTable $assetCountingItemTable) {
		$this->assetCountingItemTable = $assetCountingItemTable;
		return $this;
	}
	public function getAssetPictureTable() {
		return $this->assetPictureTable;
	}
	public function setAssetPictureTable(AssetPictureTable $assetPictureTable) {
		$this->assetPictureTable = $assetPictureTable;
		return $this;
	}
}
