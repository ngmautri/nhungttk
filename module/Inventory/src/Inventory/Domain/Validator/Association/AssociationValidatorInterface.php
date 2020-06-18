<?php
namespace Inventory\Domain\Validator\Association;

use Inventory\Domain\Association\AbstractAssociation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface AssociationValidatorInterface
{

    /**
     *
     * @param AbstractAssociation $rootEntity
     */
    public function validate(AbstractAssociation $rootEntity);
}

