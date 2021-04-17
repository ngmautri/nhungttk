<?php
namespace Application\Domain\Service;

use Application\Domain\Company\AccountChart\Validator\Contracts\AccountValidatorCollection;
use Application\Domain\Company\AccountChart\Validator\Contracts\ChartValidatorCollection;
use Application\Domain\Service\Contracts\AccountChartValidationServiceInterface;
use Inventory\Domain\Exception\InvalidArgumentException;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AccountChartValidationService implements AccountChartValidationServiceInterface
{

    protected $chartValidators;

    protected $accountValidators;

    public function __construct(ChartValidatorCollection $chartValidators, AccountValidatorCollection $accountValidators = null)
    {
        if ($chartValidators == null) {
            throw new InvalidArgumentException("Chart Validator(s) is required");
        }

        $this->chartValidators = $chartValidators;
        $this->accountValidators = $accountValidators;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Service\Contracts\AccountChartValidationServiceInterface::getChartValidators()
     */
    public function getChartValidators()
    {
        return $this->chartValidators;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Service\Contracts\AccountChartValidationServiceInterface::getAccountValidators()
     */
    public function getAccountValidators()
    {
        return $this->accountValidators;
    }
}