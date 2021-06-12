<?php
namespace Application\Domain\Company\ItemAssociation\Validator\Contracts;

use Application\Domain\Company\ItemAssociation\BaseAssociation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface ItemAssociationValidatorInterface
{

    public function validate(BaseAssociation $rootEntity);
}

