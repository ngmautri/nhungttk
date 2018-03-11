<?php
namespace HRTest;

use PHPUnit_Framework_TestCase;
use HR\Payroll\Employee;
use HR\Payroll\Payroll;
use HR\Payroll\Income\GenericIncomeComponent;
use HR\Payroll\Income\Decorator\Factory\AbstractDecoratorFactoryRegistry;
use HR\Payroll\Input\ConsolidatedPayrollInput;
use HR\Payroll\Income\Factory\AttendanceBonusFactory;
use HR\Payroll\Income\Factory\BasicSalaryFactory;
use HR\Payroll\Income\Factory\FixedAmountFactory;
use HR\Payroll\Income\Factory\LoyaltyBonusFactory;

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
            $incomeFactory= new BasicSalaryFactory(900000, "LAK");
            $incomeComponent = $incomeFactory->createIncomeComponent();
            $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
            $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
            $incomeList[] = $decoratedIncome;
            
            $incomeFactory= new FixedAmountFactory(217000, "LAK");
            $incomeComponent = $incomeFactory->createIncomeComponent();
            $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
            $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
            $incomeList[] = $decoratedIncome;
                  
            $incomeFactory= new AttendanceBonusFactory(350000, "LAK");
            $incomeComponent = $incomeFactory->createIncomeComponent();
            $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
            $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
            $incomeList[] = $decoratedIncome;
            
            $incomeFactory= new LoyaltyBonusFactory(0, "LAK");
            $incomeComponent = $incomeFactory->createIncomeComponent();
            $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
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