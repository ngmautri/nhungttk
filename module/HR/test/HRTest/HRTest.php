<?php
namespace HRTest;

use PHPUnit_Framework_TestCase;
use HR\Payroll\Income\GenericIncomeComponent;
use HR\Payroll\Income\Decorator\Factory\AbstractDecoratorFactoryRegistry;
use HR\Payroll\Input\ConsolidatedPayrollInput;
use HR\Payroll\Employee;
/**
 * Test HR
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class HRTest extends PHPUnit_Framework_TestCase
{

    public function testDBTest()
    {
        
        var_dump(AbstractDecoratorFactoryRegistry::getSupportedFactory());
        
        $employee=new Employee();
        $employee->setEmployeeCode("0651");
        $employee->setStatus("LC");
        $employee->setStartDate("2014-11-03");
        
        
        $input=new ConsolidatedPayrollInput($employee);
        $input->setActualWorkedDays(23);
        $input->setPaidSickleaves(2);
        $input->setTotalWorkingDays(26);
        $ytd=2018;
        
        // $sv = Bootstrap::getServiceManager ()->get ( 'HR\Service\EmployeeSearchService' );
        $incomeComponent=new GenericIncomeComponent("Basic salary",1000, 1000, "USD", true, true, true);
        $n=AbstractDecoratorFactoryRegistry::getDecoratorFactory("HR\Payroll\Income\Decorator\Factory\AttendanceBonusDecoratorFactory");        
        
        
        /** @var \HR\Payroll\AbstractIncomeDecorator $decoratedIncome ; */
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        echo sprintf('Identifer "%s" : Calculated salary:"%s", Des "%s"', 
            $decoratedIncome->getIdentifer(),
            $decoratedIncome->getCalculatedAmount(),
            $decoratedIncome->getDescription());            
    }
}