<?php
namespace Application\Domain\Company\ItemAttribute\Validator\Contracts;

use Application\Domain\Company\ItemAttribute\BaseAttribute;
use Application\Domain\Company\ItemAttribute\BaseAttributeGroup;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface ItemAttributeValidatorInterface
{

    /**
     *
     * @param BaseAttributeGroup $rootEntity
     * @param BaseAttribute $localEntity
     */
    public function validate(BaseAttributeGroup $rootEntity, BaseAttribute $localEntity);
}

