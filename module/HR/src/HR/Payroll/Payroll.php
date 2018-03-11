<?php
namespace HR\Payroll;

use HR\Payroll\Income\AbstractIncomeComponent;
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

    public function calculate()
    {
        $incomeList = $this->incomeList;
        $ssoIncomeAmount = 0;
        $pitIncomeAmount = 0;
        $grossAmount = 0;
        
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
        }
        
        $ssoIncome = new GenericIncomeComponent("SSO Payable", $ssoIncomeAmount, 0, "usd", FALSE, FALSE, FALSE);
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory("HR\Payroll\Income\Decorator\Factory\SSOIncomeDecoratorFactory");
        /** @var \HR\Payroll\Income\Decorator\AbstractIncomeDecorator  $decoratedIncome ; */
        $decoratedIncome = $n->createIncomeDecorator($ssoIncome, $this->payrollInput, 2018);
        echo $decoratedIncome->getDescription() . "--" . $decoratedIncome->getCalculatedAmount();
        
        $pitBase = $pitIncomeAmount-1000000 - $decoratedIncome->getCalculatedAmount();
        $pit = new GenericIncomeComponent("PIT Payable", $pitBase, 0, "usd", FALSE, FALSE, FALSE);
         
        
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory("HR\Payroll\Income\Decorator\Factory\PITIncomeDecoratorFactory");
        /** @var \HR\Payroll\Income\Decorator\AbstractIncomeDecorator  $decoratedIncome ; */
        $decoratedIncome = $n->createIncomeDecorator($pit, $this->payrollInput, 2018);
        echo $decoratedIncome->getDescription() . "--Base " . $decoratedIncome->getAmount() ."-- PIT". $decoratedIncome->getCalculatedAmount();
        
        
        echo sprintf('SSO: " "%s"; PIT:"%s"; Gross: "%s"', $ssoIncomeAmount, $pitIncomeAmount, $grossAmount);
    }
}
 