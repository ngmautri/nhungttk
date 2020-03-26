<?php
namespace HR\Payroll\Income\Decorator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SSOIncomeDecorator extends AbstractIncomeDecorator
{

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        if ($this->getAmount() > 4500000) {
            return 4500000 * 5.5 / 100;
        }
        return $this->getAmount() * 5.5 / 100;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\Decorator\AbstractIncomeDecorator::getDescription()
     */
    public function getDescription()
    {
        // TODO Auto-generated method stub
        return "SSO Employee Contribution: 5.5%";
    }
}