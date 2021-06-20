<?php
namespace Application\Domain\Company\Brand\Repository;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\Brand\BaseBrand;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface BrandCmdRepositoryInterface
{

    public function storeBrand(BaseCompany $rootEntity, BaseBrand $localEntity, $isPosting = false);

    public function removeBrand(BaseCompany $rootEntity, BaseBrand $localEntity, $isPosting = false);
}
