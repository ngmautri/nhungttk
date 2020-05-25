<?php
namespace Procure\Domain\Service;

use Procure\Domain\Service\Contracts\ValidationServiceInterface;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use InvalidArgumentException;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ValidationServiceImp implements ValidationServiceInterface
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

    public function setRowValidators($rowValidators)
    {
        $this->rowValidators = $rowValidators;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Contracts\ValidationServiceInterface::getHeaderValidators()
     */
    public function getHeaderValidators()
    {
        return $this->headerValidators;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Contracts\ValidationServiceInterface::getRowValidators()
     */
    public function getRowValidators()
    {
        return $this->rowValidators;
    }
}