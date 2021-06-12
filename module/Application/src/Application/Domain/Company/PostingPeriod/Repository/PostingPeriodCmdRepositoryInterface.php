<?php
namespace Application\Domain\Company\PostingPeriod\Repository;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\PostingPeriod\BasePostingPeriod;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface PostingPeriodCmdRepositoryInterface
{

    public function storePostingPeriod(BaseCompany $rootEntity, BasePostingPeriod $localEntity, $isPosting = false);

    public function removePostingPeriod(BaseCompany $rootEntity, BasePostingPeriod $localEntity, $isPosting = false);
}
