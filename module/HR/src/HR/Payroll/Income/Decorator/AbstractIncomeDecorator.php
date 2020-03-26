<?php
namespace HR\Payroll\Income\Decorator;

use HR\Payroll\Exception\InvalidArgumentException;
use HR\Payroll\Income\IncomeInterface;
use HR\Payroll\Input\ConsolidatedPayrollInput;

/**
 * Abstract Income Decorator to calculate income.
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
    function __construct(IncomeInterface $incomeComponent, ConsolidatedPayrollInput $consolidatedPayrollInput)
    {
        if (! $consolidatedPayrollInput instanceof ConsolidatedPayrollInput) {
            throw new InvalidArgumentException(sprintf('No Payroll Input provided!"%s" is wrong "%s".', 1, 1));
        }

        if (! $incomeComponent instanceof IncomeInterface) {
            throw new InvalidArgumentException(sprintf('Income Component "%s" is wrong "%s".', 1, 1));
        }

        $this->incomeComponent = $incomeComponent;
        $this->consolidatedPayrollInput = $consolidatedPayrollInput;
    }

    /**
     *
     * @return mixed
     *
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
     * @return \HR\Payroll\Input\ConsolidatedPayrollInput
     *
     */
    public function getConsolidatedPayrollInput()
    {
        return $this->consolidatedPayrollInput;
    }

    /**
     *
     * @param ConsolidatedPayrollInput $consolidatedPayrollInput
     *
     */
    public function setConsolidatedPayrollInput(ConsolidatedPayrollInput $consolidatedPayrollInput)
    {
        $this->consolidatedPayrollInput = $consolidatedPayrollInput;
    }

    /**
     *
     * @param mixed $identifer
     */
    public function setIdentifer($identifer)
    {
        $this->identifer = $identifer;
    }

    /**
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->incomeComponent->getDescription();
    }

    /**
     *
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\AbstractIncomeComponent::getIncomeDecoratorFactory()
     */
    public function getIncomeDecoratorFactory()
    {
        // TODO Auto-generated method stub
        return $this->incomeComponent->getIncomeDecoratorFactory();
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\AbstractIncomeComponent::getPaymentFrequency()
     */
    public function getPaymentFrequency()
    {
        // TODO Auto-generated method stub
        return $this->incomeComponent->getPaymentFrequency();
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\AbstractIncomeComponent::isPayable()
     */
    public function isPayable()
    {
        // TODO Auto-generated method stub
        return $this->incomeComponent->isPayable();
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\AbstractIncomeComponent::isPITPayable()
     */
    public function isPITPayable()
    {
        // TODO Auto-generated method stub
        return $this->incomeComponent->isPITPayable();
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\AbstractIncomeComponent::isSSOPayable()
     */
    public function isSSOPayable()
    {
        // TODO Auto-generated method stub
        return $this->incomeComponent->isSSOPayable();
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\IncomeInterface::getAmount()
     */
    public function getAmount()
    {
        // TODO Auto-generated method stub
        return $this->incomeComponent->getAmount();
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        // TODO Auto-generated method stub
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\IncomeInterface::getCurrency()
     */
    public function getCurrency()
    {
        // TODO Auto-generated method stub
        return $this->incomeComponent->getCurrency();
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\IncomeInterface::getIncomeName()
     */
    public function getIncomeName()
    {
        // TODO Auto-generated method stub
        return $this->incomeComponent->getIncomeName();
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\IncomeInterface::isDefault()
     */
    public function isDefault()
    {
        return $this->incomeComponent->isDefault();
    }
}


