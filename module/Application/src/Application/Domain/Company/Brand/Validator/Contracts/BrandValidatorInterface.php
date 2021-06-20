<?php
namespace Application\Domain\Company\Brand\Validator\Contracts;

use Application\Domain\Company\Brand\BaseBrand;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface BrandValidatorInterface
{

    public function validate(BaseBrand $rootEntity);
}

