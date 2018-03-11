<?php
namespace HR\Payroll;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use HR\Payroll\Exception\LogicException;

/**
 * Payroll Calculator
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PayrollCalculator{
    
    private $dispatcher;
    private $payrollHeader;
    private $payrollLines;
    
    /**
     * 
     * @param unknown $payrollHeader
     * @param unknown $payrollLines
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct($payrollHeader, $payrollLines, EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
        $this->payrollHeader = $payrollHeader;
        $this->payrollLines = $payrollLines;
    }
    
    
    public function calculate(){
        $payrollLines=  $this->payrollLines;
        
        foreach ($payrollLines as $payroll){
            
            if (!$payroll) {
                $message = sprintf('Place "%s" is not valid for workflow "%s".',1, 1);
                throw new LogicException($message);
            }
 
        }
    }
    
    
    
}   


