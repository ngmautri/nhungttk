<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Workflow\Controller;

use Zend\I18n\Validator\Int;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\Validator\EmailAddress;
use Zend\Mail\Message;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use MLA\Files;

use Workflow\Model\Workflow;
use Workflow\Model\WorkflowTable;


/*
 * Control Panel Controller
 */
class WFController extends AbstractActionController {
	protected $SmtpTransportService;
	protected $authService;
	protected $aclService;
	protected $userTable;
	protected $aclRoleTable;
	protected $aclResourceTable;
	protected $aclUserRoleTable;
	protected $aclRoleResourceTable;
	protected $tree;
	protected $workflowTable;
	
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}

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
		public function getWorkflowTable() {
		return $this->workflowTable;
	}
	public function setWorkflowTable(WorkflowTable $workflowTable) {
		$this->workflowTable = $workflowTable;
		return $this;
	}
	
}
