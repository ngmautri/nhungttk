<?php
namespace HRTest;

use PHPUnit_Framework_TestCase;
use HR\Payroll\Employee;
use HR\Payroll\Payroll;
use HR\Payroll\Income\GenericIncomeComponent;
use HR\Payroll\Income\Decorator\Factory\AbstractDecoratorFactoryRegistry;
use HR\Payroll\Input\ConsolidatedPayrollInput;

/**
 * test hr
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *        
 */
class HRTest extends phpunit_framework_testcase
{

    public function testHR()
    {
        // var_dump(abstractdecoratorfactoryregistry::getsupportedfactory());
        try {
            $employee = new Employee();
            $employee->setemployeecode("0651");
            $employee->setstatus("LC");
            $employee->setstartworkingdate(new \DateTime("2008-11-01"));
            
            $input = new ConsolidatedPayrollInput($employee, new \Datetime('2018-01-01'), new \Datetime('2018-01-31'));
            $input->setactualworkeddays(23);
            $input->setpaidsickleaves(2);
            $input->settotalworkingdays(26);
            $ytd = 2018;
            $incomeList = array();
            
            // $sv = bootstrap::getservicemanager ()->get ( 'hr\service\employeesearchservice' );
            $incomeComponent = new GenericIncomeComponent("basic salary", 1000000, 0, "usd", TRUE, TRUE, TRUE);
            $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory("HR\Payroll\Income\Decorator\Factory\ContractedSalaryDecoratorFactory");
            /** @var \HR\Payroll\Income\Decorator\Factory\AbstractDecoratorFactory  $decoratedIncome; */
            $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
            $incomeList[] = $decoratedIncome;
            
            $incomeComponent = new GenericIncomeComponent("Loyalty Bonus", 0, 0, "usd", TRUE, FALSE, TRUE);
            $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory("HR\Payroll\Income\Decorator\Factory\LoyaltyBonusDecoratorFactory");
            /** @var \HR\Payroll\Income\Decorator\Factory\AbstractDecoratorFactory  $decoratedIncome; */
            $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
            $incomeList[] = $decoratedIncome;
            
            
            $incomeComponent = new GenericIncomeComponent("Transportation  Allowance", 120000, 0, "usd", TRUE, FALSE, TRUE);
            $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory("HR\Payroll\Income\Decorator\Factory\TransportationAllowanceDecoratorFactory");
            /** @var \HR\Payroll\Income\Decorator\Factory\AbstractDecoratorFactory  $decoratedIncome; */
            $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
            $incomeList[] = $decoratedIncome;
            
            
            /*
             * echo sprintf('identifer" "%s"; calculated salary:"%s"; description: "%s"',
             * $decoratedincome->getidentifer(),
             * $decoratedincome->getcalculatedamount(),
             * $decoratedincome->getdescription());
             */
            
            $payroll = new Payroll($employee, $input, $incomeList);
            $payroll->calculate();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}