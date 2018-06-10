<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory\Controller;

//use Zend\I18n\Validator\Int;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;
use Zend\Http\Headers;

use Zend\Mail\Transport\Smtp as SmtpTransport;

use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail\Header\ContentType;

use MLA\Paginator;
use MLA\Files;
use Inventory\Model\SparepartPicture;
use Inventory\Model\SparepartPictureTable;
use Inventory\Model\MLASparepart;
use Inventory\Model\MLASparepartTable;
use Inventory\Model\SparepartCategoryMember;
use Inventory\Model\SparepartCategoryMemberTable;
use Inventory\Model\SparepartCategoryTable;
use Inventory\Model\SparepartMovement;
use Inventory\Model\SparepartMovementsTable;
use Inventory\Services\SparepartService;
use Procurement\Model\PurchaseRequestCartItem;
use Procurement\Model\PurchaseRequestCartItemTable;
use Inventory\Model\SparepartMinimumBalance;
use Inventory\Model\SparepartMinimumBalanceTable;

class ConsoleController extends AbstractActionController {
	protected $authService;
	protected $SmtpTransportService;
	protected $sparePartService;
	protected $userTable;
	protected $sparePartTable;
	protected $sparePartPictureTable;
	protected $sparepartMovementsTable;
	protected $sparePartCategoryTable;
	protected $sparePartCategoryMemberTable;
	protected $purchaseRequestCartItemTable;
	protected $spMinimumBalanceTable;
	protected $massage = 'NULL';
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}
	
	/**
	 * 
	 * @return \Zend\Stdlib\ResponseInterface|\Zend\View\Model\ViewModel
	 */
	public function suggestAction() {
		
		$request = $this->getRequest ();
		
		// Make sure that we are running in a console and the user has not tricked our
		// application into running this action from a public web server.
		if (! $request instanceof \Zend\Console\Request) {
			throw new \RuntimeException ( 'You can only use this action from a console-- NMT!' );
		}
		
	
		 $spareparts = $this->sparePartCategoryMemberTable->getOrderSuggestion (15,0);
		 
		 $details = '
				<table  style="font-size:10pt; border-spacing: 1px; padding:3px; border: 1px solid #cbcbcb;display: block;white-space: nowrap">';
		 	
		 $details=$details.'<tr style="border: 1px solid #cbcbcb; background-color: #f2f2f2; font-weight: bold; text-align: center;">';
		 $details=$details.'<td style="border: 1pt solid #cbcbcb;" > No.</td>';
		 $details=$details.'<td style="border: 1pt solid #cbcbcb;" > Sparepart SKU</td>';
		 $details=$details.'<td style="border: 1pt solid #cbcbcb;" > Sparepart Name</td>';
		 $details=$details.'<td style="border: 1px solid #cbcbcb; "> Sparepart / Code</td>';
		 $details=$details.'<td style="border: 1px solid #cbcbcb; "> Current <br> Balance</td>';
		 $details=$details.'<td style="border: 1px solid #cbcbcb; "> Minimum <br> Balance</td>';
		 $details=$details.'<td style="border: 1px solid #cbcbcb; "> Suggestion</td>';
		 $details=$details.'</tr>';
		 	
		 $n=0;
		 foreach ($spareparts as $item){
		 	$n=$n+1;
		 
		 	$details=$details.'<tr style="border: 1px solid #cbcbcb;vertical-align:middle" >';
			 	$details=$details.'<td style="border: 1px solid #cbcbcb;">'. $n.'</td>';
		 	$details=$details.'<td style="border: 1px solid #cbcbcb; vertical-align: middle">'. $item->tag.'</td>';
		 	$details=$details.'<td style="border: 1px solid #cbcbcb; vertical-align: middle">'. $item->name.'</td>';
		 
		 	$details=$details.'<td style="border: 1px solid #cbcbcb;text-align: center;">'. $item->code.'</td>';
		 	$details=$details.'<td style="border: 1px solid #cbcbcb;text-align: right;">'. $item->current_balance.'</td>';
		 	$details=$details.'<td style="border: 1px solid #cbcbcb;text-align: right;">'. $item->minimum_balance.'</td>';
		 	if($item->remaining_to_order<=0):
		 		$details=$details.'<td style="border: 1px solid #cbcbcb;text-align: right;">Please order Now</td>';
		 	else:
		 		$details=$details.'<td style="border: 1px solid #cbcbcb;text-align: right;">-</td>';
		 	endif;
		 	
		 	$details=$details.'</tr>';
		 }
		 $details = $details.'</table>';
		 
			
		 $transport = $this->getSmtpTransportService();
		 //$transport = $this->getServiceLocator ()->get ( 'SmtpTransportService' );
		 
		 $emailText = <<<EOT
		 
<p>Hello Sparepart Controller,</p>		 
<p>Below is the sugguestion for ordering of spare parts!</p>
<p> Please click <a href="http://laosit02/">http://laosit02/</a> for more detail!</p>

$details

<p>
Regards,<br/>
MLA Team
</p>
<p>(<em>This Email is generated by the system automatically. Please do not reply!</em>)</p>
EOT;
		 $html = new MimePart($emailText);
		 $html->type = "text/html";
		 
		 $body = new MimeMessage();
		 $body->setParts(array($html));
		 
		 // build message
		 $message = new Message ();
		 $message->addFrom ( 'mla-app@outlook.com' );
		 $message->addTo ("vvd@mascot.dk");
		 $message->addCc("mlasparepart@mascot.dk" );
		 $message->setSubject ( 'MLA - Sparepart Order Sugguestion - '.date( "m-d-Y" )  );
		 
		 $type = new ContentType();
		 $type->setType('text/html');
		 
		 $message->getHeaders()->addHeader($type);
		 $message->setBody ($emailText);
		 $message->setEncoding("UTF-8");
		 
		 // send message
		 $transport->send ( $message );
		 
	}

	

	
	// SETTER AND GETTER
	public function getAuthService() {
		return $this->authService;
	}
	public function setAuthService($authService) {
		$this->authService = $authService;
		return $this;
	}
	public function setSmtpTransportService(SmtpTransport $SmtpTransportService) {
		$this->SmtpTransportService = $SmtpTransportService;
		return $this;
	}
	public function setSparePartService(SparepartService $sparePartService) {
		$this->sparePartService = $sparePartService;
		return $this;
	}
	public function getUserTable() {
		return $this->userTable;
	}
	public function setUserTable($userTable) {
		$this->userTable = $userTable;
		return $this;
	}
	public function getSparePartTable() {
		return $this->sparePartTable;
	}
	public function setSparePartTable(MLASparepartTable $sparePartTable) {
		$this->sparePartTable = $sparePartTable;
		return $this;
	}
	public function setSparepartMovementsTable(SparepartMovementsTable $sparepartMovementsTable) {
		$this->sparepartMovementsTable = $sparepartMovementsTable;
		return $this;
	}
	public function setSparePartCategoryTable(SparepartCategoryTable $sparePartCategoryTable) {
		$this->sparePartCategoryTable = $sparePartCategoryTable;
		return $this;
	}
	public function setSparePartPictureTable(SparepartPictureTable $sparePartPictureTable) {
		$this->sparePartPictureTable = $sparePartPictureTable;
		return $this;
	}
	public function setSparePartCategoryMemberTable(SparepartCategoryMemberTable $sparePartCategoryMemberTable) {
		$this->sparePartCategoryMemberTable = $sparePartCategoryMemberTable;
		return $this;
	}
	public function getSparePartService() {
		return $this->sparePartService;
	}
	public function getSmtpTransportService() {
		return $this->SmtpTransportService;
	}
	public function getSparePartPictureTable() {
		return $this->sparePartPictureTable;
	}
	public function getSparepartMovementsTable() {
		return $this->sparepartMovementsTable;
	}
	public function getSparePartCategoryTable() {
		return $this->sparePartCategoryTable;
	}
	public function getSparePartCategoryMemberTable() {
		return $this->sparePartCategoryMemberTable;
	}
	public function getPurchaseRequestCartItemTable() {
		return $this->purchaseRequestCartItemTable;
	}
	public function setPurchaseRequestCartItemTable(PurchaseRequestCartItemTable $purchaseRequestCartItemTable) {
		$this->purchaseRequestCartItemTable = $purchaseRequestCartItemTable;
		return $this;
	}
	public function getSpMinimumBalanceTable() {
		return $this->spMinimumBalanceTable;
	}
	public function setSpMinimumBalanceTable(SparepartMinimumBalanceTable $spMinimumBalanceTable) {
		$this->spMinimumBalanceTable = $spMinimumBalanceTable;
		return $this;
	}
}
