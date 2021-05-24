<?php
namespace Inventory\Domain\Item\Variant\Validator\Contracts;

use Inventory\Domain\Item\Variant\BaseVariant;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface VariantValidatorInterface
{

    public function validate(BaseVariant $rootEntity);
}

