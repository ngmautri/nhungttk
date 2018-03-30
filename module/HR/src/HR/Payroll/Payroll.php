<?php
namespace HR\Payroll;

use HR\Payroll\Income\GenericIncomeComponent;
use HR\Payroll\Income\Decorator\Factory\AbstractDecoratorFactoryRegistry;
use HR\Payroll\Exception\LogicException;
use HR\Payroll\Income\Decorator\AbstractIncomeDecorator;

/**
 * Payroll
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Payroll
{

    private $employee;

    private $payrollInput;

    private $incomeList;

    /**
     *
     * @param unknown $employee
     * @param unknown $payrollInput
     * @param unknown $incomeList
     */
    public function __construct($employee, $payrollInput, $incomeList)
    {
        $this->employee = $employee;
        $this->payrollInput = $payrollInput;
        $this->incomeList = $incomeList;
    }

    /**
     * Calculation 
     * @throws LogicException
     */
    public function calculate()
    {
        $incomeList = $this->incomeList;
        $ssoIncomeAmount = 0;
        $pitIncomeAmount = 0;
        $grossAmount = 0;
        
        /**@var \HR\Payroll\Employee $e; */
        $e = $this->employee;
        echo $e->getStatus();
        echo $e->getEmployeeName();
        
        foreach ($incomeList as $income) {
            if (! $income instanceof AbstractIncomeDecorator) {
               throw new LogicException("Invalid argurment! IncomeComponent Decorator is expected.");
            }
               $grossAmount = $grossAmount + $income->getCalculatedAmount();
            
            if ($income->isSSOPayable()) {
                $ssoIncomeAmount = $ssoIncomeAmount + $income->getCalculatedAmount();
            }
            if ($income->isPITPayable()) {
                $pitIncomeAmount = $pitIncomeAmount + $income->getCalculatedAmount();
            }
            
            echo "\n<br> Income Name: ". $income->getIncomeName(). "\n<br> Description: ". $income->getDescription() . "--\n<br> Amount: " . $income->getAmount() ."--\n<br> Calculated Amount:". $income->getCalculatedAmount()."--\n<br>";
            
        }
        
        $ssoIncome = new GenericIncomeComponent("SSO Payable", $ssoIncomeAmount, 0, "usd", FALSE, FALSE, FALSE);
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory("HR\Payroll\Income\Decorator\Factory\SSOIncomeDecoratorFactory");
        /** @var \HR\Payroll\Income\Decorator\AbstractIncomeDecorator  $decoratedIncome ; */
        $decoratedIncome = $n->createIncomeDecorator($ssoIncome, $this->payrollInput, 2018);
          
        $pitBase = $pitIncomeAmount-1000000 - $decoratedIncome->getCalculatedAmount();
        $pit = new GenericIncomeComponent("PIT Payable", $pitBase, 0, "usd", FALSE, FALSE, FALSE);
         
        
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory("HR\Payroll\Income\Decorator\Factory\PITIncomeDecoratorFactory");
        /** @var \HR\Payroll\Income\Decorator\AbstractIncomeDecorator  $decoratedIncome ; */
        $decoratedIncome = $n->createIncomeDecorator($pit, $this->payrollInput, 2018);
        echo $decoratedIncome->getDescription() . "--Base " . $decoratedIncome->getAmount() ."-- PIT". $decoratedIncome->getCalculatedAmount();
        
        
        echo sprintf('SSO: " "%s";\n<br> PIT:"%s";\n<br> Gross: "%s"', $ssoIncomeAmount, $pitIncomeAmount, $grossAmount);
    }
}
 