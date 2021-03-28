<?php
namespace Application\Domain\Company\Validator\Contracts;

use Application\Domain\Company\Department\BaseDepartment;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface DepartmentValidatorInterface
{

    public function validate(BaseDepartment $rootEntity);
}

