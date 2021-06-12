<?php
namespace Application\Domain\Company\AccessControl\Validator\Contracts;

use Application\Domain\Company\AccessControl\BaseRole;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface RoleValidatorInterface
{

    public function validate(BaseRole $rootEntity);
}

