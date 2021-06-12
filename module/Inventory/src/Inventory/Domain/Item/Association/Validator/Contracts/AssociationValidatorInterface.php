<?php
namespace Inventory\Domain\Item\Association\Validator\Contracts;

use Inventory\Domain\Item\Variant\BaseVariant;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface AssociationValidatorInterface
{

    public function validate(BaseVariant $rootEntity);
}

