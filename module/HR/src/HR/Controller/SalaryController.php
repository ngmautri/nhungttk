<?php

namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\NmtHrSalary;
use Doctrine\ORM\EntityManager;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SalaryController extends AbstractActionController {
    
    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
    protected $doctrineEM;
    
    
    /**
     * 
     * {@inheritDoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
	public function indexAction() {
		return new ViewModel ();
	}
	
	/**
	 * Add Salary component for an contract revision
	 * @param: contract revision
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function addAction() {
	    return new ViewModel ();
	}
	
	/**
	 * Assign a Salary component for an contract revision
	 * @param: contract revision
	 *
	 * @return \Zend\View\Model\ViewModel
	 */
	public function assignAction() {
	    
	    $request = $this->getRequest();
	    $redirectUrl = $request->getHeader('Referer');
	    
	    $id = (int) $this->params()->fromQuery('target_id');
	    $token = $this->params()->fromQuery('token');
	    $criteria = array(
	        'id' => $id,
	        'token' => $token
	    );
	    
	    /**@var \Application\Entity\NmtHrContract $target ; */
	    $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrContract')->findOneBy($criteria);
	    
	    if ($target instanceof \Application\Entity\NmtHrContract) {
	        
	        $entity = new NmtHrSalary();
	        $entity->setContract($target);
	        $target->setEmployee($target->getEmployee());
	         
	        return new ViewModel(array(
	            'redirectUrl' => $redirectUrl,
	            'errors' => null,
	            'entity' => $entity,
	            'target' => $target,
	           ));
	    }
	    return $this->redirect()->toRoute('access_denied');
	}
	
	/**
	 * Edit an salary Compoent
	 * @return \Zend\View\Model\ViewModel
	 */
	public function editAction() {
	    return new ViewModel ();
	}
	
	/**
	 * Show an salary Compoent
	 * @return \Zend\View\Model\ViewModel
	 */
	public function showAction() {
	    return new ViewModel ();
	}
	
	/**
	 * Show an salary Compoent
	 * Ajax accepted only
	 * @return \Zend\View\Model\ViewModel
	 */
	public function list1Action() {
	    return new ViewModel ();
	}
	
	/**
	 * List all active salary component of an contract revision.
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
	    return new ViewModel ();
	}
	
	/**
	 *
	 * @param mixed $doctrineEM
	 */
	public function setDoctrineEM(EntityManager $doctrineEM)
	{
	    $this->doctrineEM = $doctrineEM;
	}
}
