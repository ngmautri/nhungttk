<?php
namespace HR\Payroll;

use HR\Payroll\Exception\InvalidArgumentException;

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
     * @return \HR\Payroll\IncomeInterface
     */
    public function getIncomeComponent()
    {
        return $this->incomeComponent;
    }
    /**
     * @return \HR\Payroll\ConsolidatedPayrollInput
     */
    public function getConsolidatedPayrollInput()
    {
        return $this->consolidatedPayrollInput;
    }

    /**
     * @param \HR\Payroll\ConsolidatedPayrollInput $consolidatedPayrollInput
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


}


