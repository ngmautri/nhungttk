<?php

namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SalaryController extends AbstractActionController {
	
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
	 * Ajax accepted onluy
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
}
