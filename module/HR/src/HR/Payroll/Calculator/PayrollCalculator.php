<?php
namespace HR\Payroll\Calculator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use HR\Payroll\Exception\LogicException;
use HR\Payroll\Calculator\Visitor\VisitorInterface;

/**
 * Payroll Calculator.
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PayrollCalculator{
    
    private $dispatcher;
    private $payrollHeader;
    private $payrollList;
    
    /**
     * 
     *  @param mixed $payrollHeader
     *  @param array $payrollLines
     *  @param EventDispatcherInterface $dispatcher
     */
    public function __construct($payrollHeader=null, $payrollList, EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
        $this->payrollHeader = $payrollHeader;
        $this->payrollList = $payrollList;
    }
    
    
    public function calculate(){
        $payrollList=  $this->payrollList;
        
        foreach ($payrollList as $payroll){
            $payroll->calculate();
        }
    }
    
    /**
     *  Let Payroll accept visitor
     *  @param VisitorInterface $visitor
     */
    public function accept(VisitorInterface $visitor){
        $payrollList=  $this->payrollList;
        
        foreach ($payrollList as $payroll){
            
            /**@var \HR\Payroll\Payroll $payroll ; */
            $payroll->accept($visitor);
        }
        
    }
    
    
    
}   


