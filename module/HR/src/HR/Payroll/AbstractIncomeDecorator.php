<?php
namespace HR\Payroll;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractIncomeDecorator implements IncomeInterface
{

    protected $incomeComponent;

    protected $identifer;

    /**
     *
     * @param IncomeInterface $incomeComponent
     */
    function __construct(IncomeInterface $incomeComponent)
    {
        $this->incomeComponent = $incomeComponent;
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
}


