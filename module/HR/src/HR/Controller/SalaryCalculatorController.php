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
        return new ViewModel();
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function simulateAction()
    {
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
