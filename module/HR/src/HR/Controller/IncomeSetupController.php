<?php
namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class IncomeSetupController extends AbstractActionController {
	
    /**
     * 
     * {@inheritDoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
	public function indexAction() {
		return new ViewModel ();
	}
}
