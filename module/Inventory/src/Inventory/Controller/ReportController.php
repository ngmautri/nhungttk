<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory\Controller;

use Zend\I18n\Validator\Int;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;

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

class ReportController extends AbstractActionController {
	protected $authService;
	protected $SmtpTransportService;
	protected $sparePartService;
	protected $userTable;
	protected $sparePartTable;
	protected $sparePartPictureTable;
	protected $sparepartMovementsTable;
	protected $sparePartCategoryTable;
	protected $sparePartCategoryMemberTable;
	protected $massage = 'NULL';
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
		
		$movements = $this->sparepartMovementsTable->fetchAll();
		
		return new ViewModel ( array (
				'movements' => $movements,
		) ); 
	}
	
	
	// SETTER AND GETTER
	public function getAuthService() {
		return $this->authService;
	}
	public function setAuthService($authService) {
		$this->authService = $authService;
		return $this;
	}
	public function setSmtpTransportService($SmtpTransportService) {
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
}
