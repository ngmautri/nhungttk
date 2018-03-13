<?php
namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use HR\Payroll\Income\Factory\AbstractIncomeFactoryRegistry;

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
	    
	    $incomes = AbstractIncomeFactoryRegistry::getSupportedFactory();
	    return new ViewModel(array(
	        'incomes' => $incomes
	    ));
	}
}
