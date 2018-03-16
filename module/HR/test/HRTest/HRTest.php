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
use HR\Payroll\Income\Factory\TransportationBonusFactory;
use HR\Payroll\Income\Factory\OneTimeBonusFactory;
use HR\Payroll\Income\Factory\AnnualBonusFactory;

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
            $employee->setEmployeeName("Nguyen mau Tri");
            
            $employee->setEmployeecode("0651");
            $employee->setStatus(1);
            $employee->setStartWorkingdate(new \DateTime("2008-11-01"));
             
            $employee1 = clone($employee);
            $employee1->setEmployeeName("Nguyen mau Tri");
            
            $employee1->setEmployeecode("0651");
            $employee1->setStatus(2);
            $employee1->setStartWorkingdate(new \DateTime("2008-11-01"));
            
            $diffArray = $this->objectsAreIdentical($employee, $employee1);
            var_dump($diffArray);
            
            $incomeFactory = new BasicSalaryFactory(900000, "LAK");
            $employee->setBasicSalary($incomeFactory->createIncomeComponent());
            
            $input = new ConsolidatedPayrollInput($employee, new \Datetime('2018-01-01'), new \Datetime('2018-01-31'));
            $input->setactualworkeddays(23);
            $input->setpaidsickleaves(2);
            $input->settotalworkingdays(26);
            $ytd = 2018;
            $incomeList = array();
            
            // $sv = bootstrap::getservicemanager ()->get ( 'hr\service\employeesearchservice' );
            $incomeFactory = new BasicSalaryFactory(900000, "LAK");
            $incomeComponent = $incomeFactory->createIncomeComponent();
            $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
            $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
            $incomeList[] = $decoratedIncome;
            
            $incomeFactory = new FixedAmountFactory(217000, "LAK");
            $incomeComponent = $incomeFactory->createIncomeComponent();
            $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
            $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
            $incomeList[] = $decoratedIncome;
            
            $incomeFactory = new AttendanceBonusFactory(350000, "LAK");
            $incomeComponent = $incomeFactory->createIncomeComponent();
            $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
            $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
            $incomeList[] = $decoratedIncome;
            
            $incomeFactory = new LoyaltyBonusFactory(0, "LAK");
            $incomeComponent = $incomeFactory->createIncomeComponent();
            $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
            $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
            $incomeList[] = $decoratedIncome;
            
            $incomeFactory = new TransportationBonusFactory(120000, "LAK");
            $incomeComponent = $incomeFactory->createIncomeComponent();
            $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
            $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
            $incomeList[] = $decoratedIncome;
            
            $incomeFactory = new OneTimeBonusFactory(135000, "LAK");
            $incomeComponent = $incomeFactory->createIncomeComponent();
            $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
            $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
            $incomeList[] = $decoratedIncome;
            
            $incomeFactory = new AnnualBonusFactory(900000, "LAK");
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

    /**
     *
     * @param unknown $o1
     * @param unknown $o2
     * @return bool
     */
    function objectsAreIdentical($o1, $o2)
    {
        $diffArray = array();
        
        if (get_class($o1) !== get_class($o2)) {
            return null;
        }
        
        // Now do strict(er) comparison.
        $objReflection1 = new \ReflectionObject($o1);
        $objReflection2 = new \ReflectionObject($o2);
        
        $arrProperties1 = $objReflection1->getProperties();
         
        foreach ($arrProperties1 as $p1) {
            if ($p1->isStatic()) {
                continue;
            }
            $key = sprintf('%s::%s', $p1->getDeclaringClass()->getName(), $p1->getName());
            
            //echo $key . "\n";
            $p1->setAccessible(true);
            
            $v1 = $p1->getValue($o1);
            $p2 = $objReflection2->getProperty($p1->getName());
            
            $p2->setAccessible(true);
            $v2 = $p2->getValue($o2);
            
            if (! is_object($v1)) {
       
                if ($v1 !== $v2) {
                    $diffArray[$key] = array(
                        "className" => $p1->getDeclaringClass()->getName(),
                        "fieldName" => $p1->getName(),
                        "fieldType" => gettype($v1),
                        "oldValue" => $v1,
                        "newValue" => $v2
                    );
                }
            }
        }
        
        return $diffArray;
    }

    function arraysAreIdentical(array $arr1, array $arr2): bool
    {
        $count = count($arr1);
        
        // Require that they have the same size.
        if (count($arr2) !== $count) {
            return false;
        }
        
        // Require that they have the same keys.
        $arrKeysInCommon = array_intersect_key($arr1, $arr2);
        if (count($arrKeysInCommon) !== $count) {
            return false;
        }
        
        // Require that their keys be in the same order.
        $arrKeys1 = array_keys($arr1);
        $arrKeys2 = array_keys($arr2);
        foreach ($arrKeys1 as $key => $val) {
            if ($arrKeys1[$key] !== $arrKeys2[$key]) {
                return false;
            }
        }
        
        // They do have same keys and in same order.
        foreach ($arr1 as $key => $val) {
            $bool = valuesAreIdentical($arr1[$key], $arr2[$key]);
            if ($bool === false) {
                return false;
            }
        }
        
        // All tests passed.
        return true;
    }

    function valuesAreIdentical($v1, $v2): bool
    {
        $type1 = gettype($v1);
        $type2 = gettype($v2);
        
        if ($type1 !== $type2) {
            return false;
        }
        
        switch (true) {
            case ($type1 === 'boolean' || $type1 === 'integer' || $type1 === 'double' || $type1 === 'string'):
                // Do strict comparison here.
                if ($v1 !== $v2) {
                    return false;
                }
                break;
            
            case ($type1 === 'array'):
                $bool = arraysAreIdentical($v1, $v2);
                if ($bool === false) {
                    return false;
                }
                break;
            
            case 'object':
                $bool = objectsAreIdentical($v1, $v2);
                if($bool===false){
                    return false;
                }
                break;
                
            case 'NULL':
                //Since both types were of type NULL, consider their "values" equal.
                break;
                
            case 'resource':
                //How to compare if at all?
                break;
                
            case 'unknown type':
                //How to compare if at all?
                break;
        } //end switch
        
        //All tests passed.
        return true;
    }
   
}