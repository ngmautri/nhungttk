<?php
namespace HR\Payroll\Income\Factory;

use HR\Payroll\Employee;
use HR\Payroll\Income\Decorator\Factory\AbstractDecoratorFactoryRegistry;
use HR\Payroll\Input\ConsolidatedPayrollInput;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AbstractIncomeFactoryRegistry
{

    /**
     * 
     * @param unknown $factory_class
     * @return \HR\Payroll\Income\Factory\AbstractIncomeFactory|NULL
     */
    public static function getIncomeFactory($factory_class)
    {
        $factory = new $factory_class();
        
        if($factory instanceof AbstractIncomeFactory){
            return $factory;
        }else{
            return null;
        }
    }
    
   /**
    * 
    * @return array
    */
    public static function getSupportedFactory()
    {
        $employee = new Employee();
        $employee->setemployeecode("0651");
        $employee->setstatus("LC");
        $employee->setstartworkingdate(new \DateTime("2008-11-01"));
        
        $input = new ConsolidatedPayrollInput($employee, new \Datetime('2018-01-01'), new \Datetime('2018-01-31'));
        $input->setactualworkeddays(23);
        $input->setpaidsickleaves(2);
        $input->settotalworkingdays(26);
        $ytd = 2018;
        
        $supportedFactory = array();
    
        // $sv = bootstrap::getservicemanager ()->get ( 'hr\service\employeesearchservice' );
        $incomeFactory= new BasicSalaryFactory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[BasicSalaryFactory::class] = $decoratedIncome;
        
        $incomeFactory= new FixedAmountFactory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[FixedAmountFactory::class] = $decoratedIncome;
 
        $incomeFactory= new AttendanceBonusFactory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[AttendanceBonusFactory::class] = $decoratedIncome;
        
        $incomeFactory= new TransportationBonusFactory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[TransportationBonusFactory::class] = $decoratedIncome;
        
        
        $incomeFactory= new Overtime150Factory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[Overtime150Factory::class] = $decoratedIncome;
        
        $incomeFactory= new Overtime200Factory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[Overtime200Factory::class] = $decoratedIncome;
        
        $incomeFactory= new Overtime250Factory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[Overtime250Factory::class] = $decoratedIncome;
        
        $incomeFactory= new Overtime300Factory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[Overtime300Factory::class] = $decoratedIncome;
        
        $incomeFactory= new OneTimeBonusFactory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[OneTimeBonusFactory::class] = $decoratedIncome;
        
        
        
        $incomeFactory= new AnnualBonusFactory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[AnnualBonusFactory::class] = $decoratedIncome;
        
        $incomeFactory= new OneTimeBonusFactory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[OneTimeBonusFactory::class] = $decoratedIncome;
        
        $incomeFactory= new LoadingBonusFactory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[LoadingBonusFactory::class] = $decoratedIncome;
    
        $incomeFactory= new LoyaltyBonusFactory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[LoyaltyBonusFactory::class] = $decoratedIncome;
        
        
        $incomeFactory= new HousingAllowanceFactory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[HousingAllowanceFactory::class] = $decoratedIncome;
        
        $incomeFactory= new LunchAllowanceFactory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[LunchAllowanceFactory::class] = $decoratedIncome;
        
    
        $incomeFactory= new ProductivityBonusFactory();
        $incomeComponent = $incomeFactory->createIncomeComponent();
        $n = AbstractDecoratorFactoryRegistry::getDecoratorFactory($incomeComponent->getIncomeDecoratorFactory());
        $decoratedIncome = $n->createIncomeDecorator($incomeComponent, $input, $ytd);
        $supportedFactory[ProductivityBonusFactory::class] = $decoratedIncome;
        
        return $supportedFactory;
    }
}

