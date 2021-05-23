<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Item\Variant\Validator\Contracts\VariantAttributeValidatorCollection;
use Inventory\Domain\Item\Variant\Validator\Contracts\VariantValidatorCollection;
use Inventory\Domain\Service\Contracts\VariantValidationServiceInterface;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VariantValidationService implements VariantValidationServiceInterface
{

    protected $variantValidators;

    protected $variantAttributeValidators;

    public function __construct(VariantValidatorCollection $variantValidators, VariantAttributeValidatorCollection $variantAttributeValidators = null)
    {
        if ($variantValidators == null) {
            throw new InvalidArgumentException("Variant Validator(s) is required");
        }

        $this->variantValidators = $variantValidators;
        $this->variantAttributeValidators = $variantAttributeValidators;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Contracts\VariantValidationServiceInterface::getVariantValidators()
     */
    public function getVariantValidators()
    {
        return $this->variantValidators;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Contracts\VariantValidationServiceInterface::getVariantAttributeValidators()
     */
    public function getVariantAttributeValidators()
    {
        return $this->variantAttributeValidators;
    }
}