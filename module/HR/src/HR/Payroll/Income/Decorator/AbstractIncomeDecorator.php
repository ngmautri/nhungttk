<?php
namespace HR\Payroll\Income\Decorator;

use HR\Payroll\Exception\InvalidArgumentException;
use HR\Payroll\Income\IncomeInterface;
use HR\Payroll\Input\ConsolidatedPayrollInput;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractIncomeDecorator implements IncomeInterface
{

    protected $incomeComponent;
    protected $consolidatedPayrollInput; 
    protected $identifer;
    protected $description;
    

    /**
     * 
     * @param IncomeInterface $incomeComponent
     * @param ConsolidatedPayrollInput $consolidatedPayrollInput
     * @throws InvalidArgumentException
     */
    function __construct(IncomeInterface $incomeComponent,ConsolidatedPayrollInput $consolidatedPayrollInput)
    {
        
        
        if (!$consolidatedPayrollInput instanceof ConsolidatedPayrollInput) {
            throw new InvalidArgumentException(sprintf('No Payroll Input provided!"%s" is wrong "%s".',1, 1));
        }
        
        if (!$incomeComponent instanceof IncomeInterface) {
            throw new InvalidArgumentException(sprintf('Income Component "%s" is wrong "%s".',1, 1));
        }
        
        $this->incomeComponent = $incomeComponent;
        $this->consolidatedPayrollInput = $consolidatedPayrollInput;
    }

    /**
     *
     * @return unknown
     */
    public function getIdentifer()
    {
        return $this->identifer;
    }

   /**
    * 
    * @return \HR\Payroll\Income\IncomeInterface
    */
    public function getIncomeComponent()
    {
        return $this->incomeComponent;
    }
    /**
     * 
     * @return unknown|\HR\Payroll\Input\ConsolidatedPayrollInput
     */
    public function getConsolidatedPayrollInput()
    {
        return $this->consolidatedPayrollInput;
    }

    /**
     * 
     * @param unknown $consolidatedPayrollInput
     */
    public function setConsolidatedPayrollInput($consolidatedPayrollInput)
    {
        $this->consolidatedPayrollInput = $consolidatedPayrollInput;
    }
    /**
     * @param mixed $identifer
     */
    public function setIdentifer($identifer)
    {
        $this->identifer = $identifer;
    }
    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

}


