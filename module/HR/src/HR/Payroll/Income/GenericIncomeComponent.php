<?php
namespace HR\Payroll\Income;

/**
 * Generic Income Component
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericIncomeComponent extends AbstractIncomeComponent
{

    private $incomeName;

    private $isDefault;

    private $isPITPayable;

    private $isSSOPayable;

    private $isPayable;

    private $currency;

    private $amount;

    private $calculatedAmount;

    private $paymentFrequency;

    private $decoratorFactory;

    private $description;

    /**
     * Mutable Object
     *
     * @param string $incomeName
     * @param number $amount
     * @param number $calculatedAmount
     * @param string $currency
     * @param boolean $isDefault
     * @param boolean $isPITPayable
     * @param boolean $isSSOPayable
     * @param boolean $isPayable
     * @param string $decoratorFactory
     */
    function __construct($incomeName = null, $amount, $calculatedAmount = null, $currency, $isDefault, $isPITPayable, $isSSOPayable, $isPayable, $decoratorFactory = null)
    {
        $this->incomeName = $incomeName;
        $this->amount = $amount;
        $this->calculatedAmount = $amount;
        $this->currency = $currency;
        $this->isDefault = $isDefault;
        $this->isPayable = $isPayable;
        $this->isPITPayable = $isPITPayable;
        $this->isSSOPayable = $isSSOPayable;
        $this->decoratorFactory = $decoratorFactory;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\AbstractIncomeComponent::isPITPayable()
     */
    public function isPITPayable()
    {
        return $this->isPITPayable;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\IncomeInterface::getCalculatedAmount()
     */
    public function getCalculatedAmount()
    {
        return $this->calculatedAmount;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\AbstractIncomeComponent::isSSOPayable()
     */
    public function isSSOPayable()
    {
        return $this->isSSOPayable;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\IncomeInterface::getIncomeName()
     */
    public function getIncomeName()
    {
        return $this->incomeName;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\AbstractIncomeComponent::isPayable()
     */
    public function isPayable()
    {
        return $this->isPayable;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\IncomeInterface::getAmount()
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\AbstractIncomeComponent::getIncomeDecoratorFactory()
     */
    public function getIncomeDecoratorFactory()
    {
        return $this->decoratorFactory;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Payroll\Income\IncomeInterface::getCurrency()
     */
    public function getCurrency()
    {}

    /**
     *
     * @return string
     */
    public function getDecoratorFactory()
    {
        return $this->decoratorFactory;
    }

    /**
     *
     * @param string $decoratorFactory
     */
    public function setDecoratorFactory($decoratorFactory)
    {
        $this->decoratorFactory = $decoratorFactory;
    }

    /**
     *
     * @param mixed $paymentFrequency
     */
    public function setPaymentFrequency($paymentFrequency)
    {
        $this->paymentFrequency = $paymentFrequency;
    }

    public function getPaymentFrequency()
    {
        return $this->paymentFrequency;
    }

    public function getDescription()
    {
        return $this->description;
    }

    /**
     *
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function isDefault()
    {
        return $this->isDefault;
    }
}


