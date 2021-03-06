<?php
namespace HR\Payroll\Income\Decorator;

use HR\Payroll\Income\IncomeInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface AbstractFactoryInterface
{

    public function createContractedSalaryDecorator(IncomeInterface $incomeComponent);

    public function createAttendanceDecorator(IncomeInterface $incomeComponent);

    public function createOverTimeDecorator(IncomeInterface $incomeComponent);

    public function createLoyaltyBonusDecorator(IncomeInterface $incomeComponent);

    public function createFullPaymentDecorator(IncomeInterface $incomeComponent);
}