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

/**
 * 
 * @author nmt
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
