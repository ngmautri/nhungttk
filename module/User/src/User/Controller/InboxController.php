<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace User\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Math\Rand;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;

/*
 * Control Panel Controller
 */
class InboxController extends AbstractActionController {
	
	const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
	
	protected $doctrineEM;
	protected $SmtpTransportService;
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
	}
	
	public function workflowAction() {
	    
	   $this->layout("layout/fluid");
	    
	    $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
	        "email" => $this->identity()
	    ));
	    
	    $criteria = array(
	        "agent" => $u,
	        "workitemStatus" => "ENABLED",
	    );
	    
	    // var_dump($criteria);
	    
	    $sort_criteria = array();
	    
	    if (is_null($this->params()->fromQuery('perPage'))) {
	        $resultsPerPage = 15;
	    } else {
	        $resultsPerPage = $this->params()->fromQuery('perPage');
	    }
	    ;
	    
	    if (is_null($this->params()->fromQuery('page'))) {
	        $page = 1;
	    } else {
	        $page = $this->params()->fromQuery('page');
	    }
	    ;
	    
	    $list = $this->doctrineEM->getRepository('Application\Entity\NmtWfWorkitem')->findBy($criteria, $sort_criteria);
	    $total_records = count($list);
	    $paginator = null;
	    
	    if ($total_records > $resultsPerPage) {
	        $paginator = new Paginator($total_records, $page, $resultsPerPage);
	        $list = $this->doctrineEM->getRepository('Application\Entity\NmtWfWorkitem')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
	    }
	    
	    return new ViewModel(array(
	        'list' => $list,
	        'total_records' => $total_records,
	        'paginator' => $paginator
	    ));
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
	
}
