<?php
namespace HR\Controller;

use Doctrine\ORM\EntityManager;
use HR\Payroll\Employee;
use HR\Payroll\Payroll;
use HR\Payroll\Income\Factory\AbstractIncomeFactory;
use HR\Payroll\Input\ConsolidatedPayrollInput;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use HR\Payroll\Calculator\PayrollCalculator;
use HR\Payroll\Calculator\Visitor\PayslipVisitor;
use MLA\Paginator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SalaryCalculatorController extends AbstractActionController
{

    const CHAR_LIST = "__0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $this->layout("HR/layout-fullscreen");
        return new ViewModel();
    }

    /**
     * Step1
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function payrollPeriodAction()
    {
        $this->layout("HR/layout-fullscreen");
        
        $criteria = array();
        
        $sort_criteria = array(
            "postingToDate" => "DESC"
        );
        ;
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => null
        ));
    }

    /**
     * Step1
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function inputConsolidateAction()
    {
        $this->layout("HR/layout-fullscreen");
        
        return new ViewModel();
    }

    /**
     * Step2
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function inputCheckAction()
    {
        $this->layout("HR/layout-fullscreen");
        
        $request = $this->getRequest();
        $period_id = $this->params()->fromQuery('period_id');
        
        $period_id=2;
        $criteria = array(
             'period' => $period_id
        );
        
        /**@var \Application\Entity\NmtHrPayrollInput */
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrPayrollInput')->findBy($criteria);
        $total_records = count($list);
        
        
        $criteria = array(
            'id' => $period_id,
        );
        
        /**@var \Application\Entity\NmtFinPostingPeriod $period*/
        $period = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
        
       
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'period'=>$period,
            
        ));
    }

    /**
     * Step3
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function closePeriodAction()
    {
        $this->layout("HR/layout-fullscreen");
        
        return new ViewModel();
    }

    /**
     * Step4
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function createDraftAction()
    {
        $this->layout("HR/layout-fullscreen");
        return new ViewModel();
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function simulateAction()
    {
        $this->layout("HR/layout-fullscreen");
        
        $criteria = array(
            'isActive' => 1
        );
        
        /**@var \Application\Entity\NmtHrContract $target ; */
        $contracts = $this->doctrineEM->getRepository('Application\Entity\NmtHrContract')->findBy($criteria);
        
        $payrollList = array();
        
        foreach ($contracts as $target) {
            
            $criteria = array(
                'contract' => $target->getId()
            );
            $incomes = $this->doctrineEM->getRepository('Application\Entity\NmtHrSalary')->findby($criteria);
            
            if (count($incomes) > 0) {
                
                $incomeList = array();
                $employee = new Employee($target->getEmployee()->getEmployeeCode(), $target->getEmployee()->getEmployeeName());
                
                $employee->setStatus($target->getContractStatus());
                $employee->setStartWorkingdate($target->getEffectiveFrom());
                
                $input = new ConsolidatedPayrollInput($employee, new \Datetime('2018-01-01'), new \Datetime('2018-01-31 10:30:00'));
                $input->setactualworkeddays(24);
                $input->setpaidsickleaves(0);
                $input->settotalworkingdays(26);
                $ytd = 2018;
                
                foreach ($incomes as $income) {
                    
                    /**@var \Application\Entity\NmtHrSalary $income ; */
                    $salaryFactory = $income->getSalaryFactory();
                    $incomeFactory = new $salaryFactory($income->getSalaryAmount(), "LAK");
                    
                    if ($incomeFactory instanceof AbstractIncomeFactory) {
                        $incomeComponent = $incomeFactory->createIncomeComponent();
                        $incomeList[] = $incomeComponent;
                    }
                }
                
                $payroll = new Payroll($employee, $input, $incomeList);
                $payrollList[] = $payroll;
            }
        }
        
        $calculator = new PayrollCalculator(null, $payrollList, null);
        $calculator->calculate();
        
        $v1 = new PayslipVisitor();
        $calculator->accept($v1);
        
        return new ViewModel(array());
    }

    /**
     * step5
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function adjustPayrollAction()
    {
        $this->layout("HR/layout-fullscreen");
        
        return new ViewModel();
    }

    /**
     * step5
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function submitAction()
    {
        $this->layout("HR/layout-fullscreen");
        
        return new ViewModel();
    }

    /**
     *
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     *
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}
