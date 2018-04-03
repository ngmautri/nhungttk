<?php
namespace HR\Payroll\Income\Decorator\Factory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AbstractDecoratorFactoryRegistry
{

    /**
     * 
     * @param string $factory_class
     * @return \HR\Payroll\Income\Decorator\Factory\AbstractDecoratorFactory|NULL
     */
    public static function getDecoratorFactory($factory_class)
    {
        $factory = new $factory_class();
        
        if($factory instanceof AbstractDecoratorFactory){
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
        
        $supportedFactory = array();
    
        //contract salary
        $s = new ContractedSalaryDecoratorFactory();
        $supportedFactory[] = get_class($s);
        
        $s = new AttendanceBonusDecoratorFactory();
        $supportedFactory[] = get_class($s);
        
        $s = new FullPaymentDecoratorFactory();
        $supportedFactory[] = get_class($s);
        
        $s = new OvertimeDecoratorFactory();
        $supportedFactory[] = get_class($s);
        
        $s = new TransportationAllowanceDecoratorFactory();
        $supportedFactory[] = get_class($s);
        
        $s = new ProductivityBonusDecoratorFactory();
        $supportedFactory[] = get_class($s);
        
        $s = new SSOIncomeDecoratorFactory();
        $supportedFactory[] = get_class($s);
        
        $s = new PITIncomeDecoratorFactory();
        $supportedFactory[] = get_class($s);
        
        $s = new LoyaltyBonusDecoratorFactory();
        $supportedFactory[] = get_class($s);
        
       
        return $supportedFactory;
    }
}

