<?php
namespace Application\Domain\Warehouse\Validator\Contracts;

use Application\Domain\Company\BaseCompany;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface CompanyValidatorInterface
{

    public function validate(BaseCompany $rootEntity);
}

