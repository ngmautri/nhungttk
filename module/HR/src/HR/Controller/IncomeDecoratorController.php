<?php
namespace HR\Controller;

use HR\Payroll\Income\Decorator\Factory\AbstractDecoratorFactoryRegistry;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class IncomeDecoratorController extends AbstractActionController
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $decorators = AbstractDecoratorFactoryRegistry::getSupportedFactory();
        return new ViewModel(array(
            'decorators' => $decorators
        ));
    }
}
