<?php
namespace HR\Payroll\Income\Decorator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PITIncomeDecorator extends AbstractIncomeDecorator
{

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        if ($this->getAmount() < 0) {
            return 0;
        }

        return $this->getAmount() * 0.05;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\Decorator\AbstractIncomeDecorator::getDescription()
     */
    public function getDescription()
    {
        // TODO Auto-generated method stub
        return "PIT 5%";
    }
}