<?php
namespace HR\Payroll\Income\Decorator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BasicSalaryDecorator extends AbstractIncomeDecorator
{

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        $payrollInput = $this->getConsolidatedPayrollInput();
        $payrollInput->getEmployee()->getEmployeeCode();
        $c1 = $payrollInput->getTotalWorkingDays();
        $c2 = $payrollInput->getActualWorkedDays();
        $c3 = $payrollInput->getPaidSickleaves();
        return $this->getIncomeComponent()->getAmount() * (($c2 + $c3) / $c1);
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\Decorator\AbstractIncomeDecorator::getDescription()
     */
    public function getDescription()
    {
        $des = $this->incomeComponent->getDescription() . "\n";
        $des = $des . "Calculation: Base on paid days divided by total working days in period";
        return $des;
    }
}