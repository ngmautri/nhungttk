<?php
namespace Inventory\Domain\Service\Contracts;

use Inventory\Domain\Item\Variant\Validator\Contracts\VariantAttributeValidatorCollection;
use Inventory\Domain\Item\Variant\Validator\Contracts\VariantValidatorCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface VariantValidationServiceInterface
{

    /**
     *
     * @return VariantValidatorCollection
     */
    public function getVariantValidators();

    /**
     *
     * @return VariantAttributeValidatorCollection
     */
    public function getVariantAttributeValidators();
}
