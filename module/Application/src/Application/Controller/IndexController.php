<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class IndexController extends AbstractActionController {
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		$this->layout ( "layout/fluid" );
		return new ViewModel ();
	}
	
	/**
	 *
	 * {@inheritDoc}
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function infoAction() {
		$this->layout ( "layout/user/ajax" );
		
		$model = new ViewModel();
		$model->setTerminal(true);
		
		return new $model();
	}
}
