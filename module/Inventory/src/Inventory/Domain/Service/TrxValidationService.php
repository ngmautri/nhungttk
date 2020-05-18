<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Service\Contracts\TrxValidationServiceInterface;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorCollection;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorCollection;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxValidationService implements TrxValidationServiceInterface
{

    protected $headerValidators;

    protected $rowValidators;

    public function __construct(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators = null)
    {
        if ($headerValidators == null) {
            throw new InvalidArgumentException("Header Validator(s) is required");
        }

        $this->headerValidators = $headerValidators;
        $this->rowValidators = $rowValidators;
    }

    /**
     *
     * @param \Inventory\Domain\Transaction\Validator\Contracts\RowValidatorCollection $rowValidators
     */
    public function setRowValidators($rowValidators)
    {
        $this->rowValidators = $rowValidators;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Contracts\TrxValidationServiceInterface::getHeaderValidators()
     */
    public function getHeaderValidators()
    {
        return $this->headerValidators;
    }

    /**
     *
     * @return \Inventory\Domain\Transaction\Validator\Contracts\RowValidatorCollection
     */
    public function getRowValidators()
    {
        return $this->rowValidators;
    }
}