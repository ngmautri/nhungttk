<?php
namespace HR\Payroll\Income\Decorator;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AttendanceBonusDecorator extends AbstractIncomeDecorator
{
    /**
     * 
     * {@inheritDoc}
     * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        return $this->getAmount();
    }
  
}