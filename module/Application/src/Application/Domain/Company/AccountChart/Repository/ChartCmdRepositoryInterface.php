<?php
namespace Application\Domain\Company\AccountChart\Repository;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseChart;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface ChartCmdRepositoryInterface
{

    public function storeChart(BaseCompany $rootEntity, BaseChart $localEntity, $isPosting = false);

    public function storeWholeChart(BaseCompany $rootEntity, BaseChart $localEntity, $isPosting = false);

    public function removeChart(BaseCompany $rootEntity, BaseChart $localEntity, $isPosting = false);

    public function storeAccount(BaseChart $rootEntity, BaseAccount $localEntity, $isPosting = false);

    public function removeAccount(BaseChart $rootEntity, BaseAccount $localEntity, $isPosting = false);
}
