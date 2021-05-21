<?php
namespace Application\Domain\Company\ItemAttribute\Validator\Contracts;

use Application\Domain\Company\ItemAttribute\BaseAttributeGroup;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface ItemAttributeGroupValidatorInterface
{

    public function validate(BaseAttributeGroup $rootEntity);
}

