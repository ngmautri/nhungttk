<?php
namespace HRTest;

use PHPUnit_Framework_TestCase;
use HR\Payroll\Decorator\Factory\ContractedSalaryDecoratorFactory;
use HR\Payroll\GenericIncomeComponent;
use HR\Payroll\ConsolidatedPayrollInput;
use HR\Payroll\Decorator\Factory\TransportationAllowanceDecoratorFactory;

/**
 * Test HR
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class HRTest extends PHPUnit_Framework_TestCase
{

    public function testDBTest()
    {
        $input=new ConsolidatedPayrollInput();
        $input->setActualWorkedDays(23);
        $input->setPaidSickleaves(2);
        $input->setTotalWorkingDays(26);
        $ytd=2018;
        
        // $sv = Bootstrap::getServiceManager ()->get ( 'HR\Service\EmployeeSearchService' );
        $incomeComponent=new GenericIncomeComponent("Basic salary",1000, 1000, "USD", true, true, true);
        
        
        $n = new ContractedSalaryDecoratorFactory();
        
        /** @var \HR\Payroll\AbstractIncomeDecorator $decoratedIncome ; */
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, null, $ytd);
        echo sprintf('Identifer "%s" : Calculated salary:"%s1"', 
            $decoratedIncome->getIdentifer(),
            $decoratedIncome->getCalculatedAmount());
            
    }
}