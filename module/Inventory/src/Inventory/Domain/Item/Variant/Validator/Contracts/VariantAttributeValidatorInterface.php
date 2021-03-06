<?php
namespace Inventory\Domain\Item\Variant\Validator\Contracts;

use Inventory\Domain\Item\Variant\BaseVariant;
use Inventory\Domain\Item\Variant\BaseVariantAttribute;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface VariantAttributeValidatorInterface
{

    public function validate(BaseVariant $rootEntity, BaseVariantAttribute $localEntity);
}

