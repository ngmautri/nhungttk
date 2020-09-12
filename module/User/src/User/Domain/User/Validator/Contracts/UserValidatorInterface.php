<?php
namespace User\Domain\User\Validator\Contracts;

use User\Domain\User\BaseUser;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface UserValidatorInterface
{

    public function validate(BaseUser $rootEntity);
}

