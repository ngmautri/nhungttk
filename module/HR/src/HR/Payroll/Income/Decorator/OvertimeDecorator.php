<?php
namespace HR\Payroll\Income\Decorator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OvertimeDecorator extends AbstractIncomeDecorator
{

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\Decorator\AbstractIncomeDecorator::getDescription()
     */
    public function getDescription()
    {
        return "Overtime = overtime*rate/26";
    }
}