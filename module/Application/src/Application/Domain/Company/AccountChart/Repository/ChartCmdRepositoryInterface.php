<?php
namespace Application\Domain\Company\AccountChart\Repository;

use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseChart;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface ChartCmdRepositoryInterface
{

    public function store(BaseChart $rootEntity, $isPosting = false);

    public function remove(BaseChart $rootEntity, $isPosting = false);

    public function storeAccount(BaseChart $rootEntity, BaseAccount $localEntity, $isPosting = false);

    public function removeAccount(BaseChart $rootEntity, BaseAccount $localEntity, $isPosting = false);
}
