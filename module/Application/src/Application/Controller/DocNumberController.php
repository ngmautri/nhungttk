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
use Zend\View\Model\ViewModel;
use Application\Model\AclRoleTable;
use Application\Service\DepartmentService;
use Application\Entity\NmtApplicationAclRole;
use Doctrine\ORM\EntityManager;
use Application\Entity\NmtApplicationAclUserRole;
use MLA\Paginator;
use User\Model\UserTable;
use Application\Entity\NmtApplicationAclRoleResource;
use Application\Entity\NmtApplicationDepartment;
use Application\Entity\NmtApplicationUom;

/**
 *
 * @author nmt
 *        
 */
class DocNumberController extends AbstractActionController
{

    const CHAR_LIST = "__0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ__";

    protected $doctrineEM;
    
    public function indexAction() {
    }
  
    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
        }
        
        $entity=null;
        return new ViewModel(array(
            'errors' => null,
            'entity' => $entity,
        ));
	    
	}
	
	public function editAction() {
	}
	
	public function listAction() {
	    
	    $is_active = (int) $this->params ()->fromQuery ( 'is_active' );
	    $sort_by = $this->params ()->fromQuery ( 'sort_by' );
	    $sort = $this->params ()->fromQuery ( 'sort' );
	    $currentState = $this->params ()->fromQuery ( 'currentState' );
	    
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
	    
	    $is_active = (int) $this->params()->fromQuery('is_active');
	    
	    if ($is_active == null) {
	        $is_active = 1;
	    }
	    
	    if ($sort_by == null) :
	    $sort_by = "createdOn";
	    endif;
	    
	    if ($sort == null) :
	    $sort = "DESC";
	    endif;
	    
	    $criteria=array();
	    $sort_criteria=array();
	    
	    
	    
	    /** @var \Application\Repository\NmtFinPostingPeriodRepository $p */
	    // $p = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod');
	    
	    /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod */
	    // $postingPeriod = $p->getPostingPeriodStatus(new \DateTime());
	    // echo $postingPeriod->getPeriodName() . $postingPeriod->getPeriodStatus();
	    // echo $postingPeriod;
	    
	    /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
	    //$res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
	    //$list = $res->getVendorInvoiceList($is_active,$currentState,null,$sort_by,$sort,0,0);
	    
	    $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDocNumber')->findBy($criteria, $sort_criteria, 0, 0);
	    
	    $total_records = count($list);
	    $paginator = null;
	    
	    if ($total_records > $resultsPerPage) {
	        $paginator = new Paginator($total_records, $page, $resultsPerPage);
	        //$list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
	        //$list = $res->getVendorInvoiceList($is_active,$currentState,null,$sort_by,$sort,($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
	        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDocNumber')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
	        
	    }
	    
	    return new ViewModel(array(
	        'list' => $list,
	        'total_records' => $total_records,
	        'paginator' => $paginator,
	        'is_active' => $is_active,
	        'sort_by' => $sort_by,
	        'sort' => $sort,
	        'per_pape' => $resultsPerPage,
	        'currentState' => $currentState,
	        
	        
	    ));
	}
	
	
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
	
}
