<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Doctrine\ORM\EntityManager;

use MLA\Paginator;
use Application\Entity\NmtHrEmployee;
use Zend\Validator\Date;
use Zend\Math\Rand;
use HR\Service\EmployeeSearchService;

/**
 * 
 * @author nmt
 *
 */
class EmployeeContractController extends AbstractActionController {
    
    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
    protected $doctrineEM;
    protected $employeeSearchService;
    
	
	public function indexAction() {
		return new ViewModel ();
	}
	
	/**
	 * show contract of an employee
	 * @return \Zend\View\Model\ViewModel
	 */
	public function showAction() {
	    return new ViewModel ();
	}
	
	
	/**
	 * List contract of an employee
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
	    return new ViewModel ();
	}
	
	/**
	 * List contract of an employee
	 * @return \Zend\View\Model\ViewModel
	 */
	public function list1Action() {
	    
	    $request = $this->getRequest ();
	    
	    // accepted only ajax request
	    if (! $request->isXmlHttpRequest ()) {
	        return $this->redirect ()->toRoute ( 'access_denied' );
	    }
	    ;
	    
	    $this->layout ( "layout/user/ajax" );
	    
	    
	    $sort_by = $this->params()->fromQuery('sort_by');
	    $sort = $this->params()->fromQuery('sort');
	    
	    $is_active = (int) $this->params()->fromQuery('is_active');
	    
	    if ($is_active == null) :
	    $is_active = 1;
	    endif;
	    
	    if ($sort_by == null) :
	    $sort_by = "createdOn";
	    endif;
	    
	    if ($sort == null) :
	    $sort = "DESC";
	    endif;
	    
	    if (is_null($this->params()->fromQuery('perPage'))) {
	        $resultsPerPage = 5;
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
	    
	    
	    
	    $target_id = ( int ) $this->params ()->fromQuery ( 'target_id' );
	    $token = $this->params ()->fromQuery ( 'token' );
	    $criteria = array (
	        'id' => $target_id,
	        'token' => $token
	    );
	    
	   
	    $target = $this->doctrineEM->getRepository ( 'Application\Entity\NmtHrEmployee' )->findOneBy ( $criteria );
	    
	    if ($target !== null) {
	        return new ViewModel ( array (
	            'list' => null,
	            'target' => $target,
	            'sort_by' => $sort_by,
	            'sort' => $sort,
	            'per_pape' => $resultsPerPage,
	            'is_active' => $is_active,
	        ) );
	    } else {
	        //return $this->redirect ()->toRoute ( 'access_denied' );
	    }
	}
	
	/**
	 * Show current contract of an employee
	 * @return \Zend\View\Model\ViewModel
	 */
	public function getActiveContractAction() {
	    return new ViewModel ();
	}
	
	
	/**
	 * Add new contract for employee
	 * An intinial contract has revision no = 0
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addAction() {
	    return new ViewModel ();
	}
	
	
	/**
	 * Amendment of a contract
	 * This works as following
	 *  <ul>
	 *  <li>- clone the last revised active contract  </li> 
	 *  <li>- do amendment on this cloned object  </li>
	 *  <li>- save the new contract with updated revision number </li>
	 *  </ul>
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function amendAction() {
	    return new ViewModel ();
	}
	
	/**
	 * Terminate contract 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function terminateAction() {
	    return new ViewModel ();
	}
    /**
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     * @return mixed
     */
    public function getEmployeeSearchService()
    {
        return $this->employeeSearchService;
    }

    /**
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

    /**
     * @param mixed $employeeSearchService
     */
    public function setEmployeeSearchService(EmployeeSearchService $employeeSearchService)
    {
        $this->employeeSearchService = $employeeSearchService;
    }

	
	
}
